<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Serializer;

use App\Application\Command\RegisterRunningSession;

class RegisterRunningSessionDeserializer
{
    public function deserialize($content): RegisterRunningSession
    {
        //@todo : validate data against json schema
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return new RegisterRunningSession(
            $data['id'],
            $data['distance'],
            $data['shoes']
        );
    }
}
