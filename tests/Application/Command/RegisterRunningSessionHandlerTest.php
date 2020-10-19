<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\RunningSession;
use App\Domain\RunningSessionRepository;
use App\Domain\WeatherProvider;
use PHPUnit\Framework\TestCase;
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

    public function testTheRunningSessionIRegisterIsEnrichedWithCurrentWeatherData()
    {
        //Given (Arrange)
        $this->givenCrrentTemperatureIs(15.5);

        //When (Act)
        $this->whenIRegisterARunningSession(new RegisterRunningSession(
            12,
            125.7,
            'shoes'
        ));

        //Then (Assert)
        $this->thenARunningSessionShouldBeAdded(new RunningSession(
            12,
            125.7,
            'shoes',
            15.5
        ));
    }

    private function givenCrrentTemperatureIs(float $temperature): void
    {
        $this->weatherProvider
            ->getCurrentCelciusTemperature()
            ->willReturn($temperature);
    }

    private function whenIRegisterARunningSession(RegisterRunningSession $command): void
    {
        $this->handler->handle($command);
    }

    private function thenARunningSessionShouldBeAdded(RunningSession $expectedAddedEntity)
    {
        $this->repository
            ->add($expectedAddedEntity)
            ->shouldHaveBeenCalled();
    }
}
