<?php

use App\Infrastructure\Database\PostgresRunningSessionRepository;
use App\Infrastructure\Database\PostgresRunningSessionRepositoryTest;
use App\Infrastructure\Http\CurrentConditionDeserializerTest;
use App\Infrastructure\Symfony\Serializer\RegisterRunningSessionDeserializerTest;
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
        $body = CurrentConditionDeserializerTest::createBody($temperature);

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
        $body = RegisterRunningSessionDeserializerTest::createBody($id, $distance, $shoes);
        $request = Request::create('/runningsessions/'.$id, 'PUT', [], [], [], [], $body);

        $this->response = $this->kernel->handle($request);
    }

    /**
     * @Then a running session should be added with id :id distance :distance shoes :shoes and temperature :temperature
     */
    public function aRunningSessionShouldBeAddedWith($id, $distance, $shoes, $temperature)
    {
        Assert::assertEquals(201, $this->response->getStatusCode());
        PostgresRunningSessionRepositoryTest::thenRunningSessionTableShouldContain($this->dbal, $id, [
            'distance' => $distance,
            'shoes' => $shoes,
            'temperature_celcius' => $temperature,
        ]);
    }
}
