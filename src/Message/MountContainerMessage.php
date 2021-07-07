<?php

namespace App\Message;

use App\Entity\Container;

final class MountContainerMessage
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }
}
