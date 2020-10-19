<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Controller;

use App\Application\Command\RegisterRunningSession;
use App\Application\Command\RegisterRunningSessionHandler;
use App\Domain\RunningSession;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RunningSessionControllerTest extends KernelTestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|RegisterRunningSessionHandler */
    private $registerRunningSessionHandler;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->registerRunningSessionHandler = $this->prophesize(RegisterRunningSessionHandler::class);
        self::$container->set(RegisterRunningSessionHandler::class, $this->registerRunningSessionHandler->reveal());
    }

    public function testPutRouteSendsCommandToHandlerAndDisplayItsResult()
    {
        //Given (Arrange)
        $this->givenHandlerResponseIs(new RunningSession(42, 5.5, 'Adadis Turbo2', 37.2));

        //When (Act)
        $response = $this->whenISendThisRequest(Request::create('/runningsessions/42', 'PUT', [], [], [], [], <<<EOD
{
  "id": 42,
  "distance": 5.5,
  "shoes": "Adadis Turbo2"
}
EOD
        ));

        //Then (Assert)
        $this->thenThisCommandHasBeenSentToHandler(new RegisterRunningSession(42, 5.5, 'Adadis Turbo2'));
        $this->thenTheResponseIs(201, <<<EOD
{
  "id": 42,
  "distance": 5.5,
  "shoes": "Adadis Turbo2",
  "temperatureCelcius": 37.2
}
EOD, $response);
    }

    private function givenHandlerResponseIs(RunningSession $handlerResponse)
    {
        $this->registerRunningSessionHandler
            ->handle(Argument::cetera())
            ->willReturn($handlerResponse);
    }

    private function whenISendThisRequest(Request $request): Response
    {
        return self::$kernel->handle($request);
    }

    private function thenThisCommandHasBeenSentToHandler(RegisterRunningSession $expectedCommand)
    {
        $this->registerRunningSessionHandler
            ->handle($expectedCommand)
            ->shouldHaveBeenCalled();
    }

    private function thenTheResponseIs(int $statusCode, string $expectedPayload, Response $response)
    {
        self::assertSame($statusCode, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString($expectedPayload, $response->getContent());
    }
}
