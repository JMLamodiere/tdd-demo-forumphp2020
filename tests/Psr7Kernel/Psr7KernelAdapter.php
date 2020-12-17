<?php

declare(strict_types=1);

namespace App\Psr7Kernel;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Allows communicating with the Symfony kernel using Psr7 messages.
 */
class Psr7KernelAdapter
{
    private KernelInterface $symfonyKernel;
    private HttpFoundationFactoryInterface $symfonyRequestFactory;
    private HttpMessageFactoryInterface $psr7ResponseFactory;

    public function __construct(
        KernelInterface $symfonyKernel,
        HttpFoundationFactoryInterface $symfonyRequestFactory,
        HttpMessageFactoryInterface $psr7ResponseFactory
    ) {
        $this->symfonyKernel = $symfonyKernel;
        $this->symfonyRequestFactory = $symfonyRequestFactory;
        $this->psr7ResponseFactory = $psr7ResponseFactory;
    }

    /**
     * @throws \Exception To let tests fail or expect specific exceptions
     */
    public function handleOrThrow(ServerRequestInterface $psr7ServerRequest): ResponseInterface
    {
        // See https://symfony.com/doc/4.4/components/psr7.html#converting-objects-implementing-psr-7-interfaces-to-httpfoundation
        $symfonyRequest = $this->symfonyRequestFactory->createRequest($psr7ServerRequest);

        $catchExceptions = false;
        $symfonyResponse = $this->symfonyKernel
            ->handle($symfonyRequest, HttpKernelInterface::MASTER_REQUEST, $catchExceptions);

        // See https://symfony.com/doc/4.4/components/psr7.html#converting-from-httpfoundation-objects-to-psr-7
        return $this->psr7ResponseFactory->createResponse($symfonyResponse);
    }
}
