<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use Webmozart\Assert\Assert;

class CurrentConditionDeserializer
{
    /**
     * @throws \InvalidArgumentException
     * @throws \JsonException
     */
    public function deserialize(string $body): float
    {
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        Assert::isArray($data, 'Data root should be array');
        Assert::notEmpty($data, 'Data root should not be empty');
        $firstObservation = reset($data);

        return $this->denormalizeObservation($firstObservation);
    }

    private function denormalizeObservation(array $data): float
    {
        Assert::keyExists($data, 'Temperature', 'missing Temperature key');
        Assert::keyExists($data['Temperature'], 'Metric', 'missing Temperature.Metric key');
        Assert::keyExists($data['Temperature']['Metric'], 'Value', 'missing Temperature.Metric.Value key');

        return $data['Temperature']['Metric']['Value'];
    }
}
