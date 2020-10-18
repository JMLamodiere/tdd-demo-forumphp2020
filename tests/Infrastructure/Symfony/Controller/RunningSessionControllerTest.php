<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Controller;

use App\Application\Command\RegisterRunningSession;
use App\Application\Command\RegisterRunningSessionHandler;
use App\Domain\RunningSession;
use App\Infrastructure\Symfony\Serializer\RegisterRunningSessionDeserializer;
use App\Infrastructure\Symfony\Serializer\RunningSessionNormalizer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RunningSessionControllerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|RegisterRunningSessionDeserializer */
    private $commandDeserializer;
    /** @var ObjectProphecy|RunningSessionNormalizer */
    private $responseNormalizer;
    /** @var ObjectProphecy|RegisterRunningSessionHandler */
    private $registerRunningSessionHandler;

    private RunningSessionController $controller;

    protected function setUp(): void
    {
        $this->commandDeserializer = $this->prophesize(RegisterRunningSessionDeserializer::class);
        $this->responseNormalizer = $this->prophesize(RunningSessionNormalizer::class);
        $this->registerRunningSessionHandler = $this->prophesize(RegisterRunningSessionHandler::class);

        $this->controller = new RunningSessionController(
            $this->commandDeserializer->reveal(),
            $this->responseNormalizer->reveal(),
            $this->registerRunningSessionHandler->reveal()
        );
    }

    public function testPut()
    {
        $id = 42;
        /** @var ObjectProphecy|Request $request */
        $request = $this->prophesize(Request::class);
        /** @var ObjectProphecy|RegisterRunningSession $command */
        $command = $this->prophesize(RegisterRunningSession::class);
        /** @var ObjectProphecy|RegisterRunningSession $command */
        $session = $this->prophesize(RunningSession::class);
        $data = ['normalized' => 'data'];

        $request->getContent()
            ->shouldBeCalledTimes(1)
            ->willReturn($content = 'my content');

        $this->commandDeserializer->deserialize($content)
            ->shouldBeCalledTimes(1)
            ->willReturn($command);

        $command->getId()
            ->shouldBeCalledTimes(1)
            ->willReturn($id);

        $this->registerRunningSessionHandler->handle($command)
            ->shouldBeCalledTimes(1)
            ->willReturn($session);

        $this->responseNormalizer->normalize($session)
            ->shouldBeCalledTimes(1)
            ->willReturn($data);

        $result = $this->controller->put((string) $id, $request->reveal());

        self::assertSame('{"normalized":"data"}', $result->getContent());
        self::assertSame(Response::HTTP_CREATED, $result->getStatusCode());
    }
}
