<?php

use App\Infrastructure\Database\PostgresRunningSessionRepository;
use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use WireMock\Client\WireMock;

class FeatureContext implements Context
{
    private KernelInterface $kernel;
    private Connection $dbal;
    private WireMock $wireMock;
    private ?Response $response;
    private string $accuweatherApiKey;

    public function __construct(KernelInterface $kernel, Connection $dbal, WireMock $wireMock, string $accuweatherApiKey)
    {
        $this->kernel = $kernel;
        $this->wireMock = $wireMock;
        Assert::assertTrue($this->wireMock->isAlive(), 'Wiremock should be alive');
        $this->dbal = $dbal;
        $this->accuweatherApiKey = $accuweatherApiKey;
    }

    /**
     * @BeforeScenario
     */
    public function resetState()
    {
        $this->wireMock->reset();
        $this->dbal->executeStatement('TRUNCATE TABLE '.PostgresRunningSessionRepository::TABLE_NAME);
    }

    /**
     * @Given current temperature is :temperature celcius degrees
     */
    public function currentTemperatureIs($temperature)
    {
        $uri = '/currentconditions/v1/623?apikey='.$this->accuweatherApiKey;
        $body = <<<EOD
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
			"Value": $temperature,
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

        $this->wireMock->stubFor(WireMock::get(WireMock::urlEqualTo($uri))
            ->willReturn(WireMock::aResponse()
                ->withHeader('Content-Type', 'application/json')
                ->withBody($body)));
    }

    /**
     * @When I register a running session with id :id distance :distance and shoes :shoes
     */
    public function iRegisterARunningSessionWith($id, $distance, $shoes)
    {
        $request = Request::create('/runningsessions/'.$id, 'PUT', [], [], [], [], <<<EOD
{
  "id": $id,
  "distance": $distance,
  "shoes": "$shoes"
}
EOD
);

        $this->response = $this->kernel->handle($request); //, HttpKernelInterface::MASTER_REQUEST, false);
    }

    /**
     * @Then a running session should be added with id :id distance :distance shoes :shoes and temperature :temperature
     */
    public function aRunningSessionShouldBeAddedWith($id, $distance, $shoes, $temperature)
    {
        Assert::assertEquals(201, $this->response->getStatusCode());

        $row = $this->dbal->fetchAssociative(
            'SELECT distance, shoes, temperature_celcius '
            .' FROM '.PostgresRunningSessionRepository::TABLE_NAME
            .' WHERE ID = :id', [':id' => $id]);

        Assert::assertIsArray($row, 'No session found with this id');
        Assert::assertEquals($distance, $row['distance']);
        Assert::assertEquals($shoes, $row['shoes']);
        Assert::assertEquals($temperature, $row['temperature_celcius']);
    }
}
