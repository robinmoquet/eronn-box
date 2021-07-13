<?php


namespace App\Config;


use App\Util\Utils;
use Symfony\Component\Yaml\Yaml;

class VeraCrypt
{

    private ?array $config = null;

    public function __construct(Utils $utils)
    {
        $config = [];
        if ($utils->isProd()) $config = Yaml::parseFile(dirname(dirname(__DIR__)) . '/config/veracrypt/config.yaml');
        else if ($utils->isDev()) $config = Yaml::parseFile(dirname(dirname(__DIR__)) . '/config/veracrypt/config_dev.yaml');
        $this->config = $config["veracrypt"];
    }

    public function getConfig(): array
    {
        return $this->config;
    }

}
