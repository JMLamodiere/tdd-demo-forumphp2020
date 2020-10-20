<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use PHPUnit\Framework\TestCase;

class CurrentConditionDeserializerTest extends TestCase
{
    private CurrentConditionDeserializer $deserializer;

    protected function setUp(): void
    {
        $this->deserializer = new CurrentConditionDeserializer();
    }

    public function testTemperatureIsExtractedFromBody()
    {
        // When (Act)
        $result = $this->deserializer->deserialize(self::createBody(37.2));

        // Then (Assert)
        self::assertSame(37.2, $result);
    }

    public static function createBody(float $metricTemperature = 99.9): string
    {
        return <<<EOD
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
			"Value": $metricTemperature,
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
EOD;
    }
}
