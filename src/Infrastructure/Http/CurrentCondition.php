<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

class CurrentCondition
{
    private array $observations;

    /**
     * @param Observation[] $observations
     */
    public function __construct(array $observations)
    {
        $this->observations = $observations;
    }

    /**
     * @return Observation[]
     */
    public function getObservations(): array
    {
        return $this->observations;
    }
}
