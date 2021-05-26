<?php

declare(strict_types=1);

namespace App\Domain;

use Webmozart\Assert\Assert;

class Temperature
{
    private const ABSOLUTE_ZERO = -273.15;

    private float $metricTemperature;

    public function __construct(float $metricTemperature)
    {
        Assert::greaterThanEq(
            $metricTemperature,
            self::ABSOLUTE_ZERO,
            "Cannot create temperature below absolute zero: $metricTemperature"
        );

        $this->metricTemperature = $metricTemperature;
    }

    public function getMetricTemperature(): float
    {
        return $this->metricTemperature;
    }

    public function isFreezing(): bool
    {
        return $this->metricTemperature < 0;
    }
}
