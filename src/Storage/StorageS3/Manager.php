<?php


namespace App\Storage\StorageS3;


use App\Storage\ConfigInterface;
use App\Storage\ManagerInterface;
use OpenStack\ObjectStore\v1\Models\Container;
use OpenStack\ObjectStore\v1\Models\StorageObject;

class Manager implements ManagerInterface
{

    private ?Container $container = null;
    private ConfigInterface $config;


    /**
     * Manager constructor.
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {

        $this->config = $config;
    }

    private function setContainer(): void
    {
        $container = $this->config->getOpenStack()->objectStoreV1()
            ->getContainer('{containerName}');
    }

    public function getContainer(): Container
    {

    }

    public function getObject(string $objectName): StorageObject
    {
        if ($this->container === null) $this->setContainer();
        return $this->getContainer()->getObject($objectName);
    }
}
