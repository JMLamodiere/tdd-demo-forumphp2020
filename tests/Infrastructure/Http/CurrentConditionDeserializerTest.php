<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class CurrentConditionDeserializerTest extends TestCase
{
    use ProphecyTrait;

    public function testDeserialize()
    {
        $deserializer = new CurrentConditionDeserializer();
        /** @var ObjectProphecy|ResponseInterface $response */
        $response = $this->prophesize(ResponseInterface::class);
        /** @var ObjectProphecy|StreamInterface $body */
        $body = $this->prophesize(StreamInterface::class);

        $content = json_encode([[
            'Temperature' => [
                'Metric' => [
                    'Value' => $temperature = 12.9,
                ],
            ],
        ]]);

        $response->getBody()
            ->shouldBeCalledTimes(1)
            ->willReturn($body);

        $body->getContents()
            ->shouldBeCalledTimes(1)
            ->willReturn($content);

        $result = $deserializer->deserialize($response->reveal());

        self::assertSame($temperature, $result->getObservations()[0]->getMetricTemperature());
    }
}
