<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\RestWeatherProviderInterface;
use App\Domain\RunningSession;
use App\Domain\RunningSessionRepository;
use Webmozart\Assert\Assert;

class RegisterRunningSessionHandler
{
    private RestWeatherProviderInterface $weatherProvider;
    private RunningSessionRepository $repository;

    public function __construct(RestWeatherProviderInterface $weatherProvider, RunningSessionRepository $repository)
    {
        $this->weatherProvider = $weatherProvider;
        $this->repository = $repository;
    }

    public function handle(RegisterRunningSession $command): RunningSession
    {
        $currentCondition = $this->weatherProvider->callGetCurrentCondition();
        $observations = $currentCondition->getObservations();
        Assert::notEmpty($observations, 'observations should not be empty');
        $observation = reset($observations);

        $session = new RunningSession(
            $command->getId(),
            $command->getDistance(),
            $command->getShoes(),
            $observation->getMetricTemperature()
        );

        $this->repository->add($session);

        return $session;
    }
}
