<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Domain\RestWeatherProviderInterface;
use GuzzleHttp\Client;

class RestWeatherProvider implements RestWeatherProviderInterface
{
    public const CURRENT_CONDITION_URI = 'currentconditions/v1/%d?apikey=%s';
    public const LOCATION_KEY_PARIS = 623;

    private Client $client;
    private string $apiKey;
    private CurrentConditionDeserializer $serializer;

    public function __construct(Client $client, string $apiKey, CurrentConditionDeserializer $serializer)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->serializer = $serializer;
    }

    public function callGetCurrentCondition(): CurrentCondition
    {
        $uri = sprintf(self::CURRENT_CONDITION_URI, self::LOCATION_KEY_PARIS, $this->apiKey);
        $response = $this->client->get($uri);

        if ($response->getStatusCode() >= 400) {
            throw new \RuntimeException('Cannot retrieve current condition');
        }

        return $this->serializer->deserialize($response);
    }
}
