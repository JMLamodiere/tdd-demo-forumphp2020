<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;
use Webmozart\Assert\Assert;

class CurrentConditionDeserializer
{
    public function deserialize(ResponseInterface $response): CurrentCondition
    {
        $content = $response->getBody()->getContents();
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        Assert::isArray($data, 'Data root should be array');

        return new CurrentCondition(array_map([$this, 'denormalizeObservation'], $data));
    }

    private function denormalizeObservation(array $data): Observation
    {
        Assert::keyExists($data, 'Temperature', 'missing Temperature key');
        Assert::keyExists($data['Temperature'], 'Metric', 'missing Temperature.Metric key');
        Assert::keyExists($data['Temperature']['Metric'], 'Value', 'missing Temperature.Metric.Value key');

        return new Observation($data['Temperature']['Metric']['Value']);
    }
}
