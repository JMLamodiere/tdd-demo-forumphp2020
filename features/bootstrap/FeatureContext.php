<?php

use App\Domain\RunningSessionFactory;
use App\Domain\RunningSessionRepository;
use App\Domain\WeatherProvider;
use App\Infrastructure\Symfony\Serializer\RegisterRunningSessionDeserializerTest;
use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class FeatureContext implements Context
{
    use BehatProphecyTrait;

    private KernelInterface $kernel;
    private ?Response $response;
    /** @var ObjectProphecy|WeatherProvider */
    private $weatherProvider;
    /** @var ObjectProphecy|RunningSessionRepository */
    private $runningSessionRepository;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        $this->weatherProvider = $this->prophesize(WeatherProvider::class);
        $kernel->getContainer()->set(WeatherProvider::class, $this->weatherProvider->reveal());

        $this->runningSessionRepository = $this->prophesize(RunningSessionRepository::class);
        $kernel->getContainer()->set(RunningSessionRepository::class, $this->runningSessionRepository->reveal());
    }

    /**
     * @Given current temperature is :temperature celcius degrees
     */
    public function currentTemperatureIs($temperature)
    {
        $this->weatherProvider
            ->getCurrentCelciusTemperature()
            ->willReturn($temperature);
    }

    /**
     * @When I register a running session with id :id distance :distance and shoes :shoes
     */
    public function iRegisterARunningSessionWith($id, $distance, $shoes)
    {
        $server = ['CONTENT_TYPE' => 'application/json'];
        $body = RegisterRunningSessionDeserializerTest::createBody($id, $distance, $shoes);
        $request = Request::create('/runningsessions/'.$id, 'PUT', [], [], [], $server, $body);

        $this->response = $this->kernel->handle($request);
    }

    /**
     * @Then a running session should be added with id :id distance :distance shoes :shoes and temperature :temperature
     */
    public function aRunningSessionShouldBeAddedWith($id, $distance, $shoes, $temperature)
    {
        Assert::assertEquals(201, $this->response->getStatusCode());

        $this->runningSessionRepository
            ->add(RunningSessionFactory::create(
                $id,
                $distance,
                $shoes,
                $temperature
            ))
            ->shouldHaveBeenCalled();
    }
}
