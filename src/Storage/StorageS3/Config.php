<?php


namespace App\Storage\StorageS3;


use App\Storage\ConfigInterface;
use App\Util\Utils;
use OpenStack\OpenStack;
use Symfony\Component\Yaml\Yaml;

class Config implements ConfigInterface
{
    private array $config;
    private ?OpenStack $openStack = null;

    public function __construct(Utils $utils)
    {
        if ($utils->isProd()) {
            $this->config = Yaml::parseFile(dirname(dirname(__DIR__)) . '/config/storage/config.yaml');
        } else {
            $this->config = Yaml::parseFile(dirname(dirname(__DIR__)) . '/config/storage/config_dev.yaml');
        }

        $this->setOpenStack();

    }

    function getName(): string
    {
        return $this->config["storage"]['name'];
    }

    function getPath(): string
    {
        return $this->config["storage"]['path'];
    }

    function getOpenStack(): OpenStack
    {
        return $this->openStack;
    }

    private function setOpenStack(): void
    {
        if ($this->openStack === null) {
            $this->openStack = new OpenStack([
                'authUrl' => '{authUrl}',
                'region'  => '{region}',
                'user'    => [
                    'id'       => '{userId}',
                    'password' => '{password}'
                ],
                'scope'   => ['project' => ['id' => '{projectId}']]
            ]);
        }
    }
}

