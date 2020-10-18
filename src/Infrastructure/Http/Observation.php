<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

class Observation
{
    private float $metricTemperature;

    public function __construct(float $metricTemperature)
    {
        $this->metricTemperature = $metricTemperature;
    }

    public function getMetricTemperature(): float
    {
        return $this->metricTemperature;
    }
}
