<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Controller;

use App\Application\Command\RegisterRunningSessionHandler;
use App\Infrastructure\Symfony\Serializer\RegisterRunningSessionDeserializer;
use App\Infrastructure\Symfony\Serializer\RunningSessionSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\Assert;

class RunningSessionController extends AbstractController
{
    private OpenapiRequestValidator $requestValidator;
    private RegisterRunningSessionDeserializer $commandDeserializer;
    private RunningSessionSerializer $responseSerializer;
    private RegisterRunningSessionHandler $registerRunningSessionHandler;

    public function __construct(
        OpenapiRequestValidator $requestValidator,
        RegisterRunningSessionDeserializer $commandDeserializer,
        RunningSessionSerializer $responseSerializer,
        RegisterRunningSessionHandler $registerRunningSessionHandler
    ) {
        $this->requestValidator = $requestValidator;
        $this->commandDeserializer = $commandDeserializer;
        $this->responseSerializer = $responseSerializer;
        $this->registerRunningSessionHandler = $registerRunningSessionHandler;
    }

    /**
     * @Route("/runningsessions/{id}", methods="PUT", name="runningsessions_put", requirements={"_format"="json"})
     */
    public function put(string $id, Request $request): Response
    {
        $this->requestValidator->validateRequest($request);

        $command = $this->commandDeserializer->deserialize($request->getContent());
        Assert::same($command->getId(), (int) $id, 'id must be the same in payload and uri');

        $session = $this->registerRunningSessionHandler->handle($command);

        return new JsonResponse(
            $this->responseSerializer->serialize($session),
            Response::HTTP_CREATED,
            [],
            //json is already encoded by serializer
            true
        );
    }
}
