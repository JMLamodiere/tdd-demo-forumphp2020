<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Domain\CannotGetCurrentTemperature;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use WireMock\Client\WireMock;

/**
 * @group integration
 */
class HttpAccuWeatherProviderTest extends TestCase
{
    private HttpAccuWeatherProvider $provider;
    private WireMock $wireMock;
    private string $currentConditionUri;

    protected function setUp(): void
    {
        $accuweatherApiKey = 'myApiKey';
        // See docker-compose.yml
        $host = 'wiremock';
        $port = '8080';

        $this->provider = new HttpAccuWeatherProvider(
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
        $body = CurrentConditionDeserializerTest::createBody();
        $this->wireMock->stubFor(WireMock::get(WireMock::urlEqualTo($this->currentConditionUri))
            ->willReturn(WireMock::aResponse()
                ->withHeader('Content-Type', 'application/json')
                ->withBody($body)));

        // When (Act)
        $result = $this->provider->getCurrentCelciusTemperature();

        // Then (Assert)
        // less strict assertion (type only): assertions about deserialization are in CurrentConditionDeserializerTest
        self::assertIsFloat($result);
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
