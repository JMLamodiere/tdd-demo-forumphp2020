<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Serializer;

use App\Application\Command\RegisterRunningSession;
use PHPUnit\Framework\TestCase;

class RegisterRunningSessionDeserializerTest extends TestCase
{
    private RegisterRunningSessionDeserializer $deserializer;

    protected function setUp(): void
    {
        $this->deserializer = new RegisterRunningSessionDeserializer();
    }

    public function testBodyIsCovertedToCommand()
    {
        //When (Act)
        $result = $this->deserializer->deserialize(self::createBody(42, 5.5, 'Adadis Turbo2'));

        //Then (Assert)
        self::assertEquals(new RegisterRunningSession(42, 5.5, 'Adadis Turbo2'), $result);
    }

    public static function createBody(int $id = 99, float $distance = 999.9, string $shoes = 'shoes_not_used'): string
    {
        return <<<EOD
{
  "id": $id,
  "distance": $distance,
  "shoes": "$shoes"
}
EOD;
    }
}
