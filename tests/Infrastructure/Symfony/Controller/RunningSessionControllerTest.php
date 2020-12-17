<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Controller;

use App\Application\Command\RegisterRunningSession;
use App\Application\Command\RegisterRunningSessionHandler;
use App\Domain\RunningSessionFactory;
use App\Infrastructure\Symfony\Serializer\RegisterRunningSessionDeserializerTest;
use App\Psr7Kernel\Psr7KernelTestTrait;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RunningSessionControllerTest extends KernelTestCase
{
    use ProphecyTrait;
    use Psr7KernelTestTrait;

    /** @var ObjectProphecy|RegisterRunningSessionHandler */
    private $registerRunningSessionHandler;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->initPsr7Kernel(self::$kernel->getContainer());

        $this->registerRunningSessionHandler = $this->prophesize(RegisterRunningSessionHandler::class);
        self::$container->set(RegisterRunningSessionHandler::class, $this->registerRunningSessionHandler->reveal());
    }

    public function testPutRouteSendsCommandToHandlerAndDisplayItsResult()
    {
        //Given (Arrange)
        $this->givenHandlerResponseIsARunningSession();

        //When (Act)
        $body = RegisterRunningSessionDeserializerTest::createBody(42);
        $response = $this->whenISendThisRequest($this->psr17Factory
            ->createServerRequest('PUT', '/runningsessions/42')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->psr17Factory->createStream($body))
        );

        //Then (Assert)
        //less strict assertion (type only): see RegisterRunningSessionDeserializer for conversion from json to command object
        $this->thenARegisterRunningSessionCommandHasBeenSentToHandler();

        //less strict assertion: See RunningSessionSerializerTest for json response creation
        self::assertSame(201, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
    }

    private function givenHandlerResponseIsARunningSession()
    {
        $this->registerRunningSessionHandler
            ->handle(Argument::cetera())
            ->willReturn(RunningSessionFactory::create());
    }

    private function whenISendThisRequest(ServerRequestInterface $psr7ServerRequest): ResponseInterface
    {
        return $this->psr7Kernel->handleOrThrow($psr7ServerRequest);
    }

    private function thenARegisterRunningSessionCommandHasBeenSentToHandler()
    {
        $this->registerRunningSessionHandler
            ->handle(Argument::type(RegisterRunningSession::class))
            ->shouldHaveBeenCalled();
    }
}
