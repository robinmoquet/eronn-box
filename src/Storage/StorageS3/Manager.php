<?php


namespace App\Storage\StorageS3;


use App\Entity\User;
use App\Storage\ConfigInterface;
use App\Storage\ManagerInterface;
use OpenStack\ObjectStore\v1\Models\Container;
use OpenStack\ObjectStore\v1\Models\StorageObject;
use Symfony\Component\Security\Core\Security;

class Manager implements ManagerInterface
{
    private ConfigInterface $config;
    private Security $security;

    /**
     * Manager constructor.
     * @param ConfigInterface $config
     * @param Security $security
     */
    public function __construct(ConfigInterface $config, Security $security)
    {
        $this->config = $config;
        $this->security = $security;
    }

    /**
     * @throws \Exception
     */
    public function getObject(string $objectName)
    {
        return $this->config->getClient()->getObject([
            'Bucket' => $this->config->getBucketName(),
            'Key' => $this->getObjectNameWithPrefix($objectName)
        ]);
    }

    public function downloadObject(string $objectName)
    {
        $destFileName = $this->getDownloadDestFileName($objectName);
        if (file_exists($destFileName)) throw new \Exception("This file already exist");

        if ($stream = fopen($this->getFopenPath($objectName), 'r')) {
            $this->createDownloadDestFile($objectName);
            $localStream = fopen($destFileName, 'w');
            while (!feof($stream)) {
                fwrite($localStream, fread($stream, 1024), 1024);
            }

            fclose($stream);
            fclose($localStream);
        }
    }

    /**
     * @throws \Exception
     */
    public function putObject(string $objectName, $objectBody): \Aws\Result
    {
        return $this->config->getClient()->putObject([
            'Bucket' => $this->config->getBucketName(),
            'Key' => $this->getObjectNameWithPrefix($objectName),
            'Body' => $objectBody
        ]);
    }

    /**
     * @throws \Exception
     */
    public function deleteObject(string $objectName): \Aws\Result
    {
        return $this->config->getClient()->deleteObjects([
            'Bucket' => $this->config->getBucketName(),
            'Key' => $this->getObjectNameWithPrefix($objectName)
        ]);
    }

    private function getObjectNameWithPrefix(string $objectName): string
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) throw new \Exception("User not logged");

        return $user->getBucketId() . "/" . $objectName;
    }

    private function getFopenPath(string $objectName): string
    {
        return 's3://' . $this->config->getBucketName() . '/' . $this->getObjectNameWithPrefix($objectName);
    }

    private function createDownloadDestFile(string $objectName)
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) throw new \Exception("User not logged");
        if (!file_exists($this->getDownloadDestDir())) {
            mkdir($this->getDownloadDestDir());
        }
        file_put_contents($this->getDownloadDestFileName($objectName), '');
    }

    private function getDownloadDestDir(): string
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) throw new \Exception("User not logged");

        return dirname(dirname(dirname(__DIR__))) . '/tmpStorage/container/' . $user->getBucketId();
    }

    private function getDownloadDestFileName(string $objectName)
    {
        return $this->getDownloadDestDir() . '/' . $objectName;
    }
}
