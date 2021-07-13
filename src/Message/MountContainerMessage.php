<?php

namespace App\Message;

use App\Entity\Container;

final class MountContainerMessage
{
    private Container $container;
    private string $password;

    public function __construct(Container $container, string $password)
    {
        $this->container = $container;
        $this->password = $password;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
