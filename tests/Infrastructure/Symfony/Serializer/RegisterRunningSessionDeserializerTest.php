<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Serializer;

use App\Application\Command\RegisterRunningSession;
use PHPUnit\Framework\TestCase;

class RegisterRunningSessionDeserializerTest extends TestCase
{
    public function testDeserialize()
    {
        $deserializer = new RegisterRunningSessionDeserializer();
        $content = json_encode([
            'id' => $id = 42,
            'distance' => $distance = 42,
            'shoes' => $shoes = 'my shoes please!',
        ]);

        $result = $deserializer->deserialize($content);
        $expected = new RegisterRunningSession(
            $id,
            $distance,
            $shoes
        );
        self::assertEquals($expected, $result);
    }
}
