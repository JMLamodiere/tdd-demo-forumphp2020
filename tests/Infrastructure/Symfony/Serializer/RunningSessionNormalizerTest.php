<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Serializer;

use App\Domain\RunningSession;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class RunningSessionNormalizerTest extends TestCase
{
    use ProphecyTrait;

    public function testNormalize()
    {
        $session = new RunningSession(
            55,
            107,
            'my boots',
            21.1
        );

        $normalizer = new RunningSessionNormalizer();

        $result = $normalizer->normalize($session);
        $expected = [
            'id' => $session->getId(),
            'distance' => $session->getDistance(),
            'shoes' => $session->getShoes(),
            'temperatureCelcius' => $session->getMetricTemperature(),
        ];

        self::assertSame($expected, $result);
    }
}
