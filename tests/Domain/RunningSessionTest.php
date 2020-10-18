<?php

declare(strict_types=1);

namespace App\Domain;

use PHPUnit\Framework\TestCase;

class RunningSessionTest extends TestCase
{
    public function testGetters()
    {
        $session = new RunningSession(
            $id = 25,
            $distance = 44.9,
            $shoes = 'shoes',
            $temperature = 37.2
        );

        self::assertSame($id, $session->getId());
        self::assertSame($distance, $session->getDistance());
        self::assertSame($shoes, $session->getShoes());
        self::assertSame($temperature, $session->getMetricTemperature());
    }
}
