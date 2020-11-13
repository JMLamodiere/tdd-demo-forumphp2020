<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Controller;

use App\Application\Command\RegisterRunningSession;
use App\Application\Command\RegisterRunningSessionHandler;
use App\Domain\RunningSessionFactory;
use App\Infrastructure\Symfony\Serializer\RegisterRunningSessionDeserializerTest;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

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
        $this->givenHandlerResponseIsARunningSession();

        //When (Act)
        $server = ['CONTENT_TYPE' => 'application/json'];
        $body = RegisterRunningSessionDeserializerTest::createBody(42);
        $response = $this->whenISendThisRequest(
            Request::create('/runningsessions/42', 'PUT', [], [], [], $server, $body)
        );

        //Then (Assert)
        //less strict assertion (type only): see RegisterRunningSessionDeserializer for conversion from json to command object
        $this->thenARegisterRunningSessionCommandHasBeenSentToHandler();

        //less strict assertion: See RunningSessionSerializerTest for json response creation
        self::assertSame(201, $response->getStatusCode());
        self::assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testPutRouteReturns400IfInvalidBody()
    {
        //When (Act)
        $server = ['CONTENT_TYPE' => 'application/json'];
        $body = '{"unexpected": "body"}';
        $response = $this->whenISendThisRequest(
            Request::create('/runningsessions/42', 'PUT', [], [], [], $server, $body),
            $letSymfonyConvertExceptions = true
        );

        //Then (Assert)
        self::assertSame(400, $response->getStatusCode());
        self::assertSame('application/json', $response->headers->get('Content-Type'));
    }

    private function givenHandlerResponseIsARunningSession()
    {
        $this->registerRunningSessionHandler
            ->handle(Argument::cetera())
            ->willReturn(RunningSessionFactory::create());
    }

    private function whenISendThisRequest(Request $request, $letSymfonyConvertExceptions = false): Response
    {
        //$catch=false: prevents Symfony from catching exceptions
        return self::$kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, $letSymfonyConvertExceptions);
    }

    private function thenARegisterRunningSessionCommandHasBeenSentToHandler()
    {
        $this->registerRunningSessionHandler
            ->handle(Argument::type(RegisterRunningSession::class))
            ->shouldHaveBeenCalled();
    }
}
