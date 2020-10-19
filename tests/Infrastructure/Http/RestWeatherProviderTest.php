<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Domain\CannotGetCurrentTemperature;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use WireMock\Client\WireMock;

class RestWeatherProviderTest extends KernelTestCase
{
    private RestWeatherProvider $provider;
    private WireMock $wireMock;
    private string $currentConditionUri;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->provider = self::$container->get(RestWeatherProvider::class);

        $this->wireMock = self::$container->get(WireMock::class);
        self::assertTrue($this->wireMock->isAlive(), 'Wiremock should be alive');

        $accuweatherApiKey = self::$container->getParameter('accuweather.apikey');
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
