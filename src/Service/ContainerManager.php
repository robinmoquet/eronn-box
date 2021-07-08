<?php

namespace App\Service;


use App\Entity\Container;
use App\Storage\ConfigInterface;
use App\Storage\ManagerInterface;
use Ramsey\Uuid\Uuid;

class ContainerManager
{
    private ManagerInterface $storageManager;
    private VeraCryptManager $veraCryptManager;

    /**
     * ContainerManager constructor.
     * @param ManagerInterface $storageManager
     * @param VeraCryptManager $veraCryptManager
     */
    public function __construct(ManagerInterface $storageManager, VeraCryptManager $veraCryptManager)
    {
        $this->storageManager = $storageManager;
        $this->veraCryptManager = $veraCryptManager;
    }

    function mount(Container $container)
    {
        $this->storageManager->downloadObject($container->getName());
    }

    function create(array $options)
    {
        $options["path"] = "/Users/robinmoquet/Desktop/containers/" . $options["name"] . ".hc";
        unset($options["name"]);

        $this->veraCryptManager->createContainer($options);
    }

}
