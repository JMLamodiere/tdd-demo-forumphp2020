<?php

declare(strict_types=1);

namespace App\Psr7Kernel;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;

/**
 * Allows using psr7 instead of Symfony requests and responses in tests.
 */
trait Psr7KernelTestTrait
{
    /** @var ServerRequestFactoryInterface | StreamFactoryInterface | UploadedFileFactoryInterface */
    protected $psr17Factory;
    protected Psr7KernelAdapter $psr7Kernel;

    protected function initPsr7Kernel(ContainerInterface $container): void
    {
        $this->psr17Factory = $container->get(Psr17Factory::class);
        $this->psr7Kernel = $container->get(Psr7KernelAdapter::class);
    }
}
