<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Controller;

use App\Application\Command\RegisterRunningSessionHandler;
use App\Infrastructure\Symfony\Serializer\RegisterRunningSessionDeserializer;
use App\Infrastructure\Symfony\Serializer\RunningSessionNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\Assert;

class RunningSessionController extends AbstractController
{
    private RegisterRunningSessionDeserializer $commandDeserializer;
    private RunningSessionNormalizer $responseNormalizer;
    private RegisterRunningSessionHandler $registerRunningSessionHandler;

    public function __construct(
        RegisterRunningSessionDeserializer $commandDeserializer,
        RunningSessionNormalizer $responseNormalizer,
        RegisterRunningSessionHandler $registerRunningSessionHandler
    ) {
        $this->commandDeserializer = $commandDeserializer;
        $this->responseNormalizer = $responseNormalizer;
        $this->registerRunningSessionHandler = $registerRunningSessionHandler;
    }

    /**
     * @Route("/runningsessions/{id}", methods="PUT", name="runningsessions_put", requirements={"_format"="json"})
     */
    public function put(string $id, Request $request): Response
    {
        $command = $this->commandDeserializer->deserialize($request->getContent());
        Assert::same($command->getId(), (int) $id, 'id must be the same in payload and uri');

        $session = $this->registerRunningSessionHandler->handle($command);

        return new JsonResponse($this->responseNormalizer->normalize($session), Response::HTTP_CREATED);
    }
}
