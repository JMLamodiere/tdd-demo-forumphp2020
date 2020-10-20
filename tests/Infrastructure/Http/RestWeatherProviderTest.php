<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Domain\CannotGetCurrentTemperature;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use WireMock\Client\WireMock;

class RestWeatherProviderTest extends TestCase
{
    private RestWeatherProvider $provider;
    private WireMock $wireMock;
    private string $currentConditionUri;

    protected function setUp(): void
    {
        $accuweatherApiKey = 'myApiKey';
        // See docker-compose.yml
        $host = 'wiremock';
        $port = '8080';

        $this->provider = new RestWeatherProvider(
            // See https://docs.guzzlephp.org/en/stable/quickstart.html#creating-a-client
            new Client(['base_uri' => "http://$host:$port/"]),
            $accuweatherApiKey,
            new CurrentConditionDeserializer()
        );

        // See docker-compose.yml
        $this->wireMock = WireMock::create($host, $port);
        self::assertTrue($this->wireMock->isAlive(), 'Wiremock should be alive');

        $this->currentConditionUri = '/currentconditions/v1/623?apikey='.$accuweatherApiKey;
    }

    public function testTemperatureIsExtractedFrom200Response()
    {
        // Given (Arrange)
        $this->wireMock->stubFor(WireMock::get(WireMock::urlEqualTo($this->currentConditionUri))
            ->willReturn(WireMock::aResponse()
                ->withHeader('Content-Type', 'application/json')
                ->withBody(<<<EOD
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
EOD)));

        // When (Act)
        $result = $this->provider->getCurrentCelciusTemperature();

        // Then (Assert)
        self::assertSame(37.2, $result);
    }

    public function test404ResponseIsConvertedToDomainException()
    {
        // Given (Arrange)
        $this->wireMock->stubFor(WireMock::get(WireMock::urlEqualTo($this->currentConditionUri))
            ->willReturn(WireMock::aResponse()
                ->withStatus(404)
        ));

        // Then (Assert)
        $this->expectException(CannotGetCurrentTemperature::class);

        // When (Act)
        $this->provider->getCurrentCelciusTemperature();
    }

    public function testInvalidBodyIsConvertedToDomainException()
    {
        // Given (Arrange)
        $this->wireMock->stubFor(WireMock::get(WireMock::urlEqualTo($this->currentConditionUri))
            ->willReturn(WireMock::aResponse()
                ->withHeader('Content-Type', 'application/json')
                ->withBody('invalid body')));

        // Then (Assert)
        $this->expectException(CannotGetCurrentTemperature::class);

        // When (Act)
        $this->provider->getCurrentCelciusTemperature();
    }
}
