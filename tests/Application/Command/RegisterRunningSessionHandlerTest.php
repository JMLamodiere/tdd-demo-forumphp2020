<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\RunningSession;
use App\Domain\RunningSessionRepository;
use App\Domain\WeatherProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class RegisterRunningSessionHandlerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|WeatherProvider */
    private $weatherProvider;
    /** @var ObjectProphecy|RunningSessionRepository */
    private $repository;

    private RegisterRunningSessionHandler $handler;

    protected function setUp(): void
    {
        $this->weatherProvider = $this->prophesize(WeatherProvider::class);
        $this->repository = $this->prophesize(RunningSessionRepository::class);
        $this->handler = new RegisterRunningSessionHandler(
            $this->weatherProvider->reveal(),
            $this->repository->reveal()
        );
    }

    public function testHandle()
    {
        $temperature = 15.5;
        /** @var ObjectProphecy|RegisterRunningSession $command */
        $command = $this->prophesize(RegisterRunningSession::class);
        $command->getId()
            ->shouldBeCalledTimes(1)
            ->willReturn(12);
        $command->getDistance()
            ->shouldBeCalledTimes(1)
            ->willReturn(25.7);
        $command->getShoes()
            ->shouldBeCalledTimes(1)
            ->willReturn('shoes');

        $this->weatherProvider->getCurrentCelciusTemperature()
            ->shouldBeCalledTimes(1)
            ->willReturn($temperature);

        $this->repository->add(Argument::type(RunningSession::class))
            ->shouldBeCalledTimes(1);

        $this->handler->handle($command->reveal());
    }
}
