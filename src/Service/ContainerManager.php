<?php

namespace App\Service;


use App\Entity\Container;
use App\Storage\ConfigInterface;
use App\Storage\ManagerInterface;

class ContainerManager
{
    private ManagerInterface $storageManager;

    /**
     * ContainerManager constructor.
     * @param ManagerInterface $storageManager
     */
    public function __construct(ManagerInterface $storageManager)
    {
        $this->storageManager = $storageManager;
    }

    function mount(Container $container)
    {
        $object = $this->storageManager->getObject('test.txt');
        dump($object);
    }

}
