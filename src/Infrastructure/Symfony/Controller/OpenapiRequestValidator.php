<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Controller;

use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\PSR7\RequestValidator;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OpenapiRequestValidator
{
    private HttpMessageFactoryInterface $psr7Factory;
    private RequestValidator $requestValidator;

    public function __construct(
        HttpMessageFactoryInterface $psr7Factory,
        RequestValidator $requestValidator
    ) {
        $this->psr7Factory = $psr7Factory;
        $this->requestValidator = $requestValidator;
    }

    /**
     * @throws BadRequestHttpException
     */
    public function validateRequest(Request $symfonyRequest): void
    {
        $psr7Request = $this->psr7Factory->createRequest($symfonyRequest);

        try {
            $this->requestValidator->validate($psr7Request);
        } catch (ValidationFailed $e) {
            //Real cause in previous
            $e = $e->getPrevious() ?: $e;
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}
