<?php

declare(strict_types=1);

namespace App\Domain;

class RunningSessionFactory
{
    public static function create(
        int $id = 9,
        float $distance = 999.1,
        string $shoes = 'shoes_value_not_used',
        float $metricTemperature = 98.76
    ): RunningSession {
        return new RunningSession($id, $distance, $shoes, $metricTemperature);
    }
}
