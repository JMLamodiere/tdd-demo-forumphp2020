<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\RunningSession;
use App\Domain\RunningSessionRepository;
use App\Domain\WeatherProvider;

class RegisterRunningSessionHandler
{
    private WeatherProvider $weatherProvider;
    private RunningSessionRepository $repository;

    public function __construct(WeatherProvider $weatherProvider, RunningSessionRepository $repository)
    {
        $this->weatherProvider = $weatherProvider;
        $this->repository = $repository;
    }

    public function handle(RegisterRunningSession $command): RunningSession
    {
        $currentTemperature = $this->weatherProvider->getCurrentCelciusTemperature();

        $session = new RunningSession(
            $command->getId(),
            $command->getDistance(),
            $command->getShoes(),
            $currentTemperature
        );

        $this->repository->add($session);

        return $session;
    }
}
