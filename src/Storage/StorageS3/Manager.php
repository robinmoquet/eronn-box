<?php


namespace App\Storage\StorageS3;


use App\Entity\Container;
use App\Entity\User;
use App\Mercure\Steps;
use App\Storage\ConfigInterface;
use App\Storage\ManagerInterface;
use App\Util\Utils;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class Manager implements ManagerInterface
{
    private ConfigInterface $config;
    private HubInterface $hub;
    private Utils $utils;
    private LoggerInterface $logger;

    /**
     * Manager constructor.
     * @param ConfigInterface $config
     * @param HubInterface $hub
     * @param Utils $utils
     */
    public function __construct(ConfigInterface $config, HubInterface $hub, Utils $utils, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->hub = $hub;
        $this->utils = $utils;
        $this->logger = $logger;
    }

    /**
     * @throws \Exception
     */
    public function getObject(Container $container)
    {
        return $this->config->getClient()->getObject([
            'Bucket' => $this->config->getBucketName(),
            'Key' => $this->getObjectNameWithPrefix($container)
        ]);
    }

    public function downloadObject(Container $container)
    {
        $destFileName = $this->getDownloadDestFileName($container);
        if (file_exists($destFileName)) throw new \Exception("This file already exist");

        $update = new Update(
            'container/mount-process/' . $container->getUser()->getKeysecure(),
            json_encode(['step' => Steps::DOWNLOAD_INIT])
        );
        $this->hub->publish($update);

        if ($stream = fopen($this->getFopenPath($container), 'r')) {
            $this->createDownloadDestFile($container);
            $localStream = fopen($destFileName, 'w');
            $bytesProcess = 0;
            $prevBytesProcessUpdate = 0;
            while (!feof($stream)) {
                $bytes = 1024;
                fwrite($localStream, fread($stream, $bytes), $bytes);
                $bytesProcess += $bytes;

                // run update for front for each 2MO upload
                if ($bytesProcess >= $prevBytesProcessUpdate + ($bytes * 2000)) {
                    $prevBytesProcessUpdate = $bytesProcess;
                    $update = new Update(
                        'container/mount-process/' . $container->getUser()->getKeysecure(),
                        json_encode([
                            'step' => Steps::DOWNLOAD_PROGRESS,
                            'progress' => $this->utils->getPercentDownload($bytesProcess, $container->getSize())
                        ])
                    );
                    $this->hub->publish($update);
                }
            }

            fclose($stream);
            fclose($localStream);

            $update = new Update(
                'container/mount-process/' . $container->getUser()->getKeysecure(),
                json_encode(['step' => Steps::DOWNLOAD_END])
            );
            $this->hub->publish($update);
        }
    }

    /**
     * @throws \Exception
     */
    public function putObject(Container $container, $objectBody): \Aws\Result
    {
        return $this->config->getClient()->putObject([
            'Bucket' => $this->config->getBucketName(),
            'Key' => $this->getObjectNameWithPrefix($container),
            'Body' => $objectBody
        ]);
    }

    /**
     * @throws \Exception
     */
    public function deleteObject(Container $container): \Aws\Result
    {
        return $this->config->getClient()->deleteObjects([
            'Bucket' => $this->config->getBucketName(),
            'Key' => $this->getObjectNameWithPrefix($container)
        ]);
    }

    private function getObjectNameWithPrefix(Container $container): string
    {
        $user = $container->getUser();

        return $user->getKeysecure() . "/" . $this->getObjectName($container);
    }

    private function getFopenPath(Container $container): string
    {
        return 's3://' . $this->config->getBucketName() . '/' . $this->getObjectNameWithPrefix($container);
    }

    private function createDownloadDestFile(Container $container)
    {
        $user = $container->getUser();
        if (!file_exists($this->getDownloadDestDir($container))) {
            mkdir($this->getDownloadDestDir($container));
        }
        file_put_contents($this->getDownloadDestFileName($container), '');
    }

    private function getDownloadDestDir(Container $container): string
    {
        return $container->getDownloadDestDir();
    }

    private function getDownloadDestFileName(Container $container)
    {
        return $this->getDownloadDestDir($container) . '/' . $this->getObjectName($container);
    }

    private function getObjectName(Container $container): string
    {
        return $container->getName() . "." . $container->getExt();
    }
}
