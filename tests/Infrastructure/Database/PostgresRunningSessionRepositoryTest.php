<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\RunningSession;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class PostgresRunningSessionRepositoryTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|Connection */
    private $dbal;

    private PostgresRunningSessionRepository $repository;

    protected function setUp(): void
    {
        $this->dbal = $this->prophesize(Connection::class);
        $this->repository = new PostgresRunningSessionRepository($this->dbal->reveal());
    }

    public function testAdd()
    {
        /** @var ObjectProphecy|QueryBuilder $queryBuilder */
        $queryBuilder = $this->prophesize(QueryBuilder::class);

        /** @var ObjectProphecy|RunningSession $session */
        $session = $this->prophesize(RunningSession::class);
        $session->getId()
            ->shouldBeCalledTimes(1)
            ->willReturn($id = 12);
        $session->getDistance()
            ->shouldBeCalledTimes(1)
            ->willReturn($distance = 25.7);
        $session->getShoes()
            ->shouldBeCalledTimes(1)
            ->willReturn($shoes = 'shoes');
        $session->getMetricTemperature()
            ->shouldBeCalledTimes(1)
            ->willReturn($temperature = 25.3);

        $this->dbal->createQueryBuilder()
            ->shouldBeCalledTimes(1)
            ->willReturn($queryBuilder);

        $queryBuilder->insert(PostgresRunningSessionRepository::TABLE_NAME)
            ->shouldBeCalledTimes(1)
            ->willReturn($queryBuilder);

        $queryBuilder->setValue(Argument::cetera())
            ->shouldBeCalledTimes(4)
            ->willReturn($queryBuilder);

        $queryBuilder->setParameter(':id', $id)
            ->shouldBeCalledTimes(1)
            ->willReturn($queryBuilder);
        $queryBuilder->setParameter(':distance', $distance)
            ->shouldBeCalledTimes(1)
            ->willReturn($queryBuilder);
        $queryBuilder->setParameter(':shoes', $shoes)
            ->shouldBeCalledTimes(1)
            ->willReturn($queryBuilder);
        $queryBuilder->setParameter(':celcius', $temperature)
            ->shouldBeCalledTimes(1)
            ->willReturn($queryBuilder);
        $queryBuilder->execute()
            ->shouldBeCalledTimes(1)
            ->willReturn(1);

        $this->repository->add($session->reveal());
    }
}
