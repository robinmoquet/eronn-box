<?php


namespace App\Storage\StorageS3;


use App\Storage\ConfigInterface;
use App\Util\Utils;
use Aws\S3\S3Client;
use Aws\Sdk;
use OpenStack\OpenStack;
use Symfony\Component\Yaml\Yaml;

class Config implements ConfigInterface
{
    private array $config;
    private Sdk $sdk;
    private S3Client $client;

    public function __construct(Utils $utils)
    {
        if ($utils->isProd()) {
            $config = Yaml::parseFile(dirname(dirname(dirname(__DIR__))) . '/config/storage/config.yaml');
        } else {
            $config = Yaml::parseFile(dirname(dirname(dirname(__DIR__))) . '/config/storage/config_dev.yaml');
        }

        $this->config = $config["storage"];
        $sdkConfig = [
            'version' => $this->config['version'],
            'region' => $this->config['region']
        ];

        $this->sdk = new Sdk($sdkConfig);
        $this->client = $this->sdk->createS3();
        $this->client->registerStreamWrapper();
    }

    public function getClient(): S3Client
    {
        return $this->client;
    }

    public function getBucketName(): string
    {
        return $this->config['name'];
    }

}

