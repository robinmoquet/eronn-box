<?php


namespace App\Storage;


use OpenStack\OpenStack;

interface ConfigInterface
{

    function getOpenStack(): OpenStack;

    /**
     * Name of the storage
     *
     * @return string
     */
    function getName(): string;

    /**
     * Path to storage
     *
     * @return string
     */
    function getPath(): string;

}
