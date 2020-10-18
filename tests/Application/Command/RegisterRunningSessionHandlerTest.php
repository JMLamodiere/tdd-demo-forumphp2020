<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\RestWeatherProviderInterface;
use App\Domain\RunningSession;
use App\Domain\RunningSessionRepository;
use App\Infrastructure\Http\CurrentCondition;
use App\Infrastructure\Http\Observation;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class RegisterRunningSessionHandlerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|RestWeatherProviderInterface */
    private $weatherProvider;
    /** @var ObjectProphecy|RunningSessionRepository */
    private $repository;

    private RegisterRunningSessionHandler $handler;

    protected function setUp(): void
    {
        $this->weatherProvider = $this->prophesize(RestWeatherProviderInterface::class);
        $this->repository = $this->prophesize(RunningSessionRepository::class);
        $this->handler = new RegisterRunningSessionHandler(
            $this->weatherProvider->reveal(),
            $this->repository->reveal()
        );
    }

    public function testHandle()
    {
        /** @var ObjectProphecy|CurrentCondition $condition */
        $condition = $this->prophesize(CurrentCondition::class);
        /** @var ObjectProphecy|Observation $observation */
        $observation = $this->prophesize(Observation::class);
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

        $this->weatherProvider->callGetCurrentCondition()
            ->shouldBeCalledTimes(1)
            ->willReturn($condition);
        $condition->getObservations()
            ->shouldBeCalledTimes(1)
            ->willReturn([$observation]);
        $observation->getMetricTemperature()
            ->willReturn($temperature);

        $this->repository->add(Argument::type(RunningSession::class))
            ->shouldBeCalledTimes(1)
            ->willReturn(2);

        $this->handler->handle($command->reveal());
    }
}
