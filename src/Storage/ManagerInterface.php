<?php


namespace App\Storage;


use App\Entity\Container;

interface ManagerInterface
{

    public function getObject(Container $container);
    public function putObject(Container $container, $objectBody);
    public function deleteObject(Container $container);
    public function downloadObject(Container $container);

}
