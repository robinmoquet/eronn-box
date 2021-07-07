<?php

namespace App\Controller;

use App\Entity\Container;
use App\Message\MountContainerMessage;
use App\Repository\ContainerRepository;
use App\Service\ContainerManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/container', name: 'container_')]
class ContainerController extends AbstractController
{
    private ContainerManager $containerManager;
    private MessageBusInterface $bus;
    private ContainerRepository $containerRepository;

    /**
     * ContainerController constructor.
     * @param ContainerManager $containerManager
     * @param MessageBusInterface $bus
     */
    public function __construct(
        ContainerManager $containerManager,
        MessageBusInterface $bus,
        ContainerRepository $containerRepository
    )
    {
        $this->containerManager = $containerManager;
        $this->bus = $bus;
        $this->containerRepository = $containerRepository;
    }

    #[Route('/mount/{id}', name: 'mount')]
    public function mount(int $id): Response
    {
        $container = $this->containerRepository->find($id);
        if ($container === null) throw new \Exception("Container not found !");

        $this->bus->dispatch(new MountContainerMessage($container));
        return new JsonResponse(['res' => 'ok']);
    }
}
