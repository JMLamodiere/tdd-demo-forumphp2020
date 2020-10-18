<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\RunningSession;
use App\Domain\RunningSessionRepository;
use Doctrine\DBAL\Connection;

class PostgresRunningSessionRepository implements RunningSessionRepository
{
    public const TABLE_NAME = 'RUNNING_SESSION';

    private Connection $dbal;

    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    public function add(RunningSession $session): int
    {
        $queryBuilder = $this->dbal->createQueryBuilder();

        $queryBuilder
            ->insert(self::TABLE_NAME)
            ->setValue('ID', ':id')->setParameter(':id', $session->getId())
            ->setValue('DISTANCE', ':distance')->setParameter(':distance', $session->getDistance())
            ->setValue('SHOES', ':shoes')->setParameter(':shoes', $session->getShoes())
            ->setValue('TEMPERATURE_CELCIUS', ':celcius')->setParameter(':celcius', $session->getMetricTemperature())
        ;

        return $queryBuilder->execute();
    }
}
