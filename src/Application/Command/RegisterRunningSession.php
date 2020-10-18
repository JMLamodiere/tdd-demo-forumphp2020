<?php

declare(strict_types=1);

namespace App\Application\Command;

class RegisterRunningSession
{
    private int $id;
    private float $distance;
    private string $shoes;

    public function __construct(int $id, float $distance, string $shoes)
    {
        $this->id = $id;
        $this->distance = $distance;
        $this->shoes = $shoes;
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
}
