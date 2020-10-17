<?php

declare(strict_types=1);

namespace App\Domain;

use App\Infrastructure\Http\CurrentCondition;
use GuzzleHttp\Exception\GuzzleException;

interface RestWeatherProviderInterface
{
    /**
     * @throws GuzzleException
     */
    public function callGetCurrentCondition(): CurrentCondition;
}
