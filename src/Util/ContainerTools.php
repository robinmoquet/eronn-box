<?php


namespace App\Util;


use App\Config\VeraCrypt;
use App\Entity\Container;

class ContainerTools
{
    private VeraCrypt $veraCryptConfig;

    /**
     * ContainerTools constructor.
     * @param VeraCrypt $veraCryptConfig
     */
    public function __construct(VeraCrypt $veraCryptConfig)
    {
        $this->veraCryptConfig = $veraCryptConfig;
    }

    public function getMountDirectory(Container $container): string
    {
        return $this->veraCryptConfig->getConfig()['base_mount_dir'] . $container->getUser()->getKeysecure() . '/' . $container->getName();
    }

    public function isMount(Container $container)
    {
        return file_exists($this->getMountDirectory($container));
    }

}
