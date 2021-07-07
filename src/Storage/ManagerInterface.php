<?php


namespace App\Storage;


use OpenStack\ObjectStore\v1\Models\Container;
use OpenStack\ObjectStore\v1\Models\StorageObject;

interface ManagerInterface
{

    /**
     * @param string $objectName
     * @return StorageObject
     */
    public function getObject(string $objectName): StorageObject;

}
