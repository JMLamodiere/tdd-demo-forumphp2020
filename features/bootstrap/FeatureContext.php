<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class FeatureContext implements Context
{
    private KernelInterface $kernel;

    private ?Response $response;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @When I call the Hello page
     */
    public function iCallTheHelloPage()
    {
        $this->response = $this->kernel->handle(Request::create('/hello', 'GET'));
    }

    /**
     * @Then It should say hello to :person
     */
    public function itShouldSayHelloTo($person)
    {
        Assert::assertNotNull($this->response, 'No response received');
        Assert::assertSame(200, $this->response->getStatusCode(), 'Response status code should be 200');
        $expected = <<<EOD
{
    "hello": "$person"
}
EOD;
        Assert::assertJsonStringEqualsJsonString($expected, $this->response->getContent());
    }
}
