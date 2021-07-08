<?php


namespace App\Storage;


use OpenStack\ObjectStore\v1\Models\Container;
use OpenStack\ObjectStore\v1\Models\StorageObject;

interface ManagerInterface
{

    public function getObject(string $objectName);
    public function putObject(string $objectName, $objectBody);
    public function deleteObject(string $objectName);
    public function downloadObject(string $objectName);

}
