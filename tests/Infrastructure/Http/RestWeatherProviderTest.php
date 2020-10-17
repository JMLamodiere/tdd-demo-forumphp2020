<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;

class RestWeatherProviderTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|Client */
    private $client;
    private string $apiKey;
    /** @var ObjectProphecy|CurrentConditionDeserializer */
    private $serializer;

    private RestWeatherProvider $provider;

    protected function setUp(): void
    {
        $this->client = $this->prophesize(Client::class);
        $this->apiKey = 'key';
        $this->serializer = $this->prophesize(CurrentConditionDeserializer::class);

        $this->provider = new RestWeatherProvider(
            $this->client->reveal(),
            $this->apiKey,
            $this->serializer->reveal()
        );
    }

    public function testCallGetCurrentCondition()
    {
        /** @var ObjectProphecy|ResponseInterface $response */
        $response = $this->prophesize(ResponseInterface::class);

        $observations = [new Observation($temperature = 42.9)];
        $condition = new CurrentCondition($observations);

        $uri = sprintf(
            RestWeatherProvider::CURRENT_CONDITION_URI,
            RestWeatherProvider::LOCATION_KEY_PARIS,
            $this->apiKey
        );

        $this->client->get($uri)
            ->shouldBeCalledTimes(1)
            ->willReturn($response);

        $response->getStatusCode()
            ->shouldBeCalledTimes(1)
            ->willReturn(200);

        $this->serializer->deserialize($response)
            ->shouldBeCalledTimes(1)
            ->willReturn($condition);

        $result = $this->provider->callGetCurrentCondition();

        self::assertSame($observations, $result->getObservations());
        self::assertSame($temperature, $result->getObservations()[0]->getMetricTemperature());
    }

    public function testCallGetCurrentConditionException()
    {
        /** @var ObjectProphecy|ResponseInterface $response */
        $response = $this->prophesize(ResponseInterface::class);

        $uri = sprintf(
            RestWeatherProvider::CURRENT_CONDITION_URI,
            RestWeatherProvider::LOCATION_KEY_PARIS,
            $this->apiKey
        );

        $this->client->get($uri)
            ->shouldBeCalledTimes(1)
            ->willReturn($response);

        $response->getStatusCode()
            ->shouldBeCalledTimes(1)
            ->willReturn(404);

        $this->expectException(\RuntimeException::class);

        $this->provider->callGetCurrentCondition();
    }
}
