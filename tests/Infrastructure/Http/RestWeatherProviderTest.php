<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Domain\CannotGetCurrentTemperature;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class RestWeatherProviderTest extends TestCase
{
    use ProphecyTrait;

    private MockHandler $guzzleMockHandler;
    private string $apiKey;
    private RestWeatherProvider $provider;

    protected function setUp(): void
    {
        $this->guzzleMockHandler = new MockHandler();
        $guzzleClient = new Client(['handler' => new HandlerStack($this->guzzleMockHandler)]);

        $this->apiKey = 'key';
        $this->provider = new RestWeatherProvider(
            $guzzleClient,
            $this->apiKey,
            new CurrentConditionDeserializer()
        );
    }

    public function testTemperatureIsExtractedFrom200Response()
    {
        // Given (Arrange)
        $this->givenAccuWeatherResponseIs(new Response(200, ['Content-Type' => 'application/json'], <<<EOD
[{
	"LocalObservationDateTime": "2020-10-17T17:50:00+02:00",
	"EpochTime": 1602949800,
	"WeatherText": "Mostly cloudy",
	"WeatherIcon": 6,
	"HasPrecipitation": false,
	"PrecipitationType": null,
	"IsDayTime": true,
	"Temperature": {
		"Metric": {
			"Value": 37.2,
			"Unit": "C",
			"UnitType": 17
		},
		"Imperial": {
			"Value": 55.0,
			"Unit": "F",
			"UnitType": 18
		}
	},
	"MobileLink": "http://m.accuweather.com/en/fr/paris/623/current-weather/623?lang=en-us",
	"Link": "http://www.accuweather.com/en/fr/paris/623/current-weather/623?lang=en-us"
}]
EOD));

        // When (Act)
        $result = $this->provider->getCurrentCelciusTemperature();

        // Then (Assert)
        self::assertSame(37.2, $result);

        $this->thenRequestSentShouldHaveBeen('GET', 'currentconditions/v1/623?apikey='.$this->apiKey);
    }

    public function test404ResponseIsConvertedToDomainException()
    {
        // Given (Arrange)
        $this->givenAccuWeatherResponseIs(RequestException::create(
            new Request('GET', 'uri'),
            new Response(404)
        ));

        // Then (Assert)
        $this->expectException(CannotGetCurrentTemperature::class);

        // When (Act)
        $this->provider->getCurrentCelciusTemperature();
    }

    public function testInvalidBodyIsConvertedToDomainException()
    {
        // Given (Arrange)
        $this->givenAccuWeatherResponseIs(
            new Response(200, ['Content-Type' => 'application/json'], 'invalid body')
        );

        // Then (Assert)
        $this->expectException(CannotGetCurrentTemperature::class);

        // When (Act)
        $this->provider->getCurrentCelciusTemperature();
    }

    /**
     * @param ResponseInterface|Throwable|PromiseInterface|callable $response
     */
    private function givenAccuWeatherResponseIs($response)
    {
        $this->guzzleMockHandler->append($response);
    }

    private function thenRequestSentShouldHaveBeen(string $method, string $uri)
    {
        $requestSent = $this->guzzleMockHandler->getLastRequest();
        self::assertSame($method, $requestSent->getMethod());
        self::assertSame($uri, (string) $requestSent->getUri());
    }
}
