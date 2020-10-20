<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Serializer;

use App\Domain\RunningSession;

class RunningSessionSerializer
{
    /**
     * @throws \JsonException
     */
    public function serialize(RunningSession $session): string
    {
        $data = [
            'id' => $session->getId(),
            'distance' => $session->getDistance(),
            'shoes' => $session->getShoes(),
            'temperatureCelcius' => $session->getMetricTemperature(),
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}
