<?php

declare(strict_types=1);

namespace App\Domain;

class RunningSession
{
    private int $id;
    private float $distance;
    private string $shoes;
    private Temperature $temperature;

    public function __construct(int $id, float $distance, string $shoes, Temperature $temperature)
    {
        $this->id = $id;
        $this->distance = $distance;
        $this->shoes = $shoes;
        $this->temperature = $temperature;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function getShoes(): string
    {
        return $this->shoes;
    }

    public function getMetricTemperature(): float
    {
        return $this->temperature->getMetricTemperature();
    }

    public function isFreezing(): bool
    {
        return $this->temperature->isFreezing();
    }
}
