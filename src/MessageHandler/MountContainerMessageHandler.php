<?php

namespace App\MessageHandler;

use App\Message\MountContainerMessage;
use App\Service\ContainerManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class MountContainerMessageHandler implements MessageHandlerInterface
{
    private ContainerManager $containerManager;

    public function __construct(ContainerManager $containerManager)
    {
        $this->containerManager = $containerManager;
    }

    public function __invoke(MountContainerMessage $message)
    {
        $this->containerManager->mount($message->getContainer(), $message->getPassword());
    }
}
