<?php


namespace App\Util;


use App\Config\VeraCrypt;
use App\Entity\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Utils
{

    private ParameterBagInterface $params;

    /**
     * Utils constructor.
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->params = $parameterBag;
    }

    public function isProd(): bool
    {
        return $this->params->get('kernel.environment') === "prod";
    }

    public function isDev(): bool
    {
        return $this->params->get('kernel.environment') === "dev";
    }

    public function getPercentDownload(int $bytesProcess, int $size): int
    {
        return (int) round(($bytesProcess / $size) * 100);
    }
}
