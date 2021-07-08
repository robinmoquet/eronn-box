<?php


namespace App\Storage;


use Aws\S3\S3Client;
use OpenStack\OpenStack;

interface ConfigInterface
{

    public function getClient(): S3Client;
    public function getBucketName(): string;

}
