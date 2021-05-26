<?php

declare(strict_types=1);

namespace App\Domain;

use PHPUnit\Framework\TestCase;

class TemperatureTest extends TestCase
{
    public function testIsNotFreezingAboveZero()
    {
        //Given
        $temperature = new Temperature(25.7);

        //When
        $result = $temperature->isFreezing();

        //Then
        self::assertFalse($result, 'temperature should not be freezing');
    }

    public function testIsFreezingBelowZero()
    {
        //Given
        $temperature = new Temperature(-12);

        //When
        $result = $temperature->isFreezing();

        //Then
        self::assertTrue($result, 'temperature should be freezing');
    }
}
