<?php

declare(strict_types=1);

namespace App\Domain;

interface WeatherProvider
{
    /**
     * @throws CannotGetCurrentTemperature
     */
    public function getCurrentCelciusTemperature(): float;
}
