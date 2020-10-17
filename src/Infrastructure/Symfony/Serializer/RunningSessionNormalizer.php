<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Serializer;

use App\Domain\RunningSession;

class RunningSessionNormalizer
{
    public function normalize(RunningSession $session): array
    {
        return [
            'id' => $session->getId(),
            'distance' => $session->getDistance(),
            'shoes' => $session->getShoes(),
            'temperatureCelcius' => $session->getMetricTemperature(),
        ];
    }
}
