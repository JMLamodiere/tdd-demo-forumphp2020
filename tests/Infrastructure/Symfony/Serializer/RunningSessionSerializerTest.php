<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Serializer;

use App\Domain\RunningSessionFactory;
use PHPUnit\Framework\TestCase;

class RunningSessionSerializerTest extends TestCase
{
    private RunningSessionSerializer $serializer;

    protected function setUp(): void
    {
        $this->serializer = new RunningSessionSerializer();
    }

    public function testRunningSessionIsConvertedToJson()
    {
        // When (Act)
        $session = RunningSessionFactory::create(42, 5.5, 'Adadis Turbo2', 37.2);

        $result = $this->serializer->serialize($session);

        // Then (Assert)
        $expectedJson = <<<EOD
{
  "id": 42,
  "distance": 5.5,
  "shoes": "Adadis Turbo2",
  "temperatureCelcius": 37.2
}
EOD;
        self::assertJsonStringEqualsJsonString($expectedJson, $result);
    }
}
