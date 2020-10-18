<?php

declare(strict_types=1);

namespace App\Domain;

interface RunningSessionRepository
{
    public function add(RunningSession $session): int;
}
