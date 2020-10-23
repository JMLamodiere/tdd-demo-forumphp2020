<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Domain\CannotGetCurrentTemperature;
use App\Domain\WeatherProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HttpAccuWeatherProvider implements WeatherProvider
{
    // See https://developer.accuweather.com/accuweather-current-conditions-api/apis
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

    /**
     * @throws CannotGetCurrentTemperature
     */
    public function getCurrentCelciusTemperature(): float
    {
        $uri = sprintf(self::CURRENT_CONDITION_URI, self::LOCATION_KEY_PARIS, $this->apiKey);

        try {
            $response = $this->client->get($uri);
        } catch (GuzzleException $previous) {
            throw new CannotGetCurrentTemperature('Cannot retrieve current condition', 0, $previous);
        }

        try {
            return $this->serializer->deserialize($response->getBody()->getContents());
        } catch (\Exception $previous) {
            throw new CannotGetCurrentTemperature('Cannot decode current condition', 0, $previous);
        }
    }
}
