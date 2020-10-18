<?php

declare(strict_types=1);

namespace App\Application\Command;

use PHPUnit\Framework\TestCase;

class RegisterRunningSessionTest extends TestCase
{
    public function testGetters()
    {
        $session = new RegisterRunningSession(
            $id = 12,
            $distance = 25.4,
            $shoes = 'my shoes'
        );

        self::assertSame($id, $session->getId());
        self::assertSame($distance, $session->getDistance());
        self::assertSame($shoes, $session->getShoes());
    }
}
