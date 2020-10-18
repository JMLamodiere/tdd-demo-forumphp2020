<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Domain\CannotGetCurrentTemperature;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use JsonException;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\RequestInterface;
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

    public function testGetCurrentCelciusTemperature()
    {
        /** @var ObjectProphecy|ResponseInterface $response */
        $response = $this->prophesize(ResponseInterface::class);

        $temperature = 42.9;

        $uri = sprintf(
            RestWeatherProvider::CURRENT_CONDITION_URI,
            RestWeatherProvider::LOCATION_KEY_PARIS,
            $this->apiKey
        );

        $this->client->get($uri)
            ->shouldBeCalledTimes(1)
            ->willReturn($response);

        $this->serializer->deserialize($response)
            ->shouldBeCalledTimes(1)
            ->willReturn($temperature);

        $result = $this->provider->getCurrentCelciusTemperature();

        self::assertSame($temperature, $result);
    }

    public function testGetCurrentCelciusTemperatureClientException()
    {
        $uri = sprintf(
            RestWeatherProvider::CURRENT_CONDITION_URI,
            RestWeatherProvider::LOCATION_KEY_PARIS,
            $this->apiKey
        );

        $this->client->get($uri)
            ->shouldBeCalledTimes(1)
            ->willThrow(new ClientException(
                'Not Found',
                $this->prophesize(RequestInterface::class)->reveal(),
                $this->prophesize(ResponseInterface::class)->reveal()
            ));

        $this->expectException(CannotGetCurrentTemperature::class);

        $this->provider->getCurrentCelciusTemperature();
    }

    public function testGetCurrentCelciusTemperatureDeserializeException()
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

        $this->serializer->deserialize($response)
            ->shouldBeCalledTimes(1)
            ->willThrow(new JsonException());

        $this->expectException(CannotGetCurrentTemperature::class);

        $this->provider->getCurrentCelciusTemperature();
    }
}
