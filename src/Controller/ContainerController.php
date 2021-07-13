<?php

namespace App\Controller;

use App\Entity\User;
use App\Message\MountContainerMessage;
use App\Repository\ContainerRepository;
use App\Service\ContainerManager;
use App\Service\VeraCryptManager;
use App\Util\ContainerTools;
use App\Util\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/container', name: 'container_')]
class ContainerController extends AbstractController
{
    private ContainerManager $containerManager;
    private MessageBusInterface $bus;
    private ContainerRepository $containerRepository;
    private ContainerTools $containerTools;

    /**
     * ContainerController constructor.
     * @param ContainerManager $containerManager
     * @param MessageBusInterface $bus
     * @param ContainerRepository $containerRepository
     * @param ContainerTools $containerTools
     */
    public function __construct(
        ContainerManager $containerManager,
        MessageBusInterface $bus,
        ContainerRepository $containerRepository,
        ContainerTools $containerTools
    )
    {
        $this->containerManager = $containerManager;
        $this->bus = $bus;
        $this->containerRepository = $containerRepository;
        $this->containerTools = $containerTools;
    }

    #[Route('/mount/{keysecure}', name: 'mount')]
    public function mount(string $keysecure, Request $request, VeraCryptManager $manager): Response
    {
        $container = $this->containerRepository->findOneBy(['keysecure' => $keysecure, 'user' => $this->getUser()]);
        if ($container === null) throw new \Exception("Container not found !");
        $content = $request->toArray();
        $password = $content['password'];

        $this->bus->dispatch(new MountContainerMessage($container, $password));
        return new JsonResponse(['status' => 'success', 'topic' => 'container/mount-process/' . $this->getUser()->getKeysecure()]);
    }

    #[Route('/create', name: 'create')]
    public function create(): Response
    {
        $options = [
            "name" => "test4",
            "size" => "1M",
            "password" => "test",
        ];

        $this->containerManager->create($options);

        return new JsonResponse(["res" => "ok"]);
    }

    #[Route('/list', name: 'list')]
    public function list(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $containersArray = $user->getContainers();
        $containers = [];
        foreach ($containersArray as $container) {
            $containers[] = array_merge($container->toArray(), [
                'isMount' => $this->containerTools->isMount($container),
                'mountDirectory' => $this->containerTools->getMountDirectory($container)
            ]);
        }

        return new JsonResponse([
            "containers" => $containers
        ]);
    }

}
