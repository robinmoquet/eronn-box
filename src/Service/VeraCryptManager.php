<?php


namespace App\Service;


use App\Config\VeraCrypt;
use App\Entity\Container;
use App\Mercure\Steps;
use App\Util\ContainerTools;
use App\Util\Utils;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class VeraCryptManager
{

    private HubInterface $hub;
    private VeraCrypt $veraCryptConfig;
    private Utils $utils;
    private ContainerTools $containerTools;

    /**
     * VeraCryptManager constructor.
     * @param HubInterface $hub
     * @param VeraCrypt $veraCryptConfig
     * @param Utils $utils
     * @param ContainerTools $containerTools
     */
    public function __construct(HubInterface $hub, VeraCrypt $veraCryptConfig, Utils $utils, ContainerTools $containerTools)
    {
        $this->hub = $hub;
        $this->veraCryptConfig = $veraCryptConfig;
        $this->utils = $utils;
        $this->containerTools = $containerTools;
    }

    public function createContainer(array $tmpOptions)
    {
        $options = [
            "encryption" => "AES",
            "filesystem" => "FAT",
            "volume-type" => "normal",
            "hash" => "sha512",
            "random-source" => "tmp",
            "non-interactive" => ""
        ];
        $options = array_merge($tmpOptions, $options);

        $randomPath = $this->createRandomFile();
        $options["random-source"] = $randomPath;

        $command = $this->getCommandLineForCreate($options);
        $res = exec($command, $output);
        unlink($randomPath);
        dd($res);
    }

    public function decryptContainer(Container $container, string $password)
    {
        $update = new Update(
            'container/mount-process/' . $container->getUser()->getKeysecure(),
            json_encode(['step' => Steps::DECRYPT_INIT])
        );
        $this->hub->publish($update);

        $options = [
            "path" => $container->getDownloadDestDir() . '/' . $container->getNameWithExt(),
            "mountDir" => $this->veraCryptConfig->getConfig()['base_mount_dir'] . $container->getUser()->getKeysecure() . "/" . $container->getName(),
            "password" => $password,
            "protect-hidden" => "no",
            "non-interactive" => ""
        ];

        $command = $this->getCommandLineForDecrypt($options);
        $this->createDirForMount($container);
        $res = exec($command, $output);

        if ($this->containerTools->isMount($container)) {
            $update = new Update(
                'container/mount-process/' . $container->getUser()->getKeysecure(),
                json_encode(['step' => Steps::DECRYPT_END])
            );
            $this->hub->publish($update);
        } else {
            $update = new Update(
                'container/mount-process/' . $container->getUser()->getKeysecure(),
                json_encode(['step' => Steps::DECRYPT_FAILED])
            );
            $this->hub->publish($update);
        }
    }

    private function getCommandLineForCreate(array $options): string
    {
        $command = "~/bin/veracrypt -t --create {$options['path']}";
        unset($options['path']);
        foreach ($options as $key => $value) {
            if ($value === "") $command .= " --{$key}";
            else $command .= " --{$key}={$value}";
        }
        return $command;
    }

    private function createRandomFile(): string
    {
        $path = dirname(dirname(__DIR__)) . '/var/cache/random/' . Uuid::uuid4();
        file_put_contents($path, random_bytes(320));
        return $path;
    }

    private function getCommandLineForDecrypt($options): string
    {
        $command = "/Users/robinmoquet/bin/veracrypt -t";
        foreach ($options as $key => $value) {
            if ($key !== "path" && $key !== "mountDir") {
                if ($value === "") $command .= " --{$key}";
                else $command .= " --{$key}={$value}";
            }
        }
        $command .= " {$options['path']} {$options['mountDir']} --filesystem=FAT";
        return $command;
    }

    private function createDirForMount(Container $container)
    {
        $path = $this->veraCryptConfig->getConfig()['base_mount_dir'] . $container->getUser()->getKeysecure() . '/' . $container->getName();
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
}
