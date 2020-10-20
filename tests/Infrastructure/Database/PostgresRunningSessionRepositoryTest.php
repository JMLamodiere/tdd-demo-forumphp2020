<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\RunningSessionFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 */
class PostgresRunningSessionRepositoryTest extends TestCase
{
    private Connection $dbal;
    private PostgresRunningSessionRepository $repository;

    protected function setUp(): void
    {
        //see https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#getting-a-connection
        $connectionParams = [
            // Same as .env.test
            'url' => 'postgres://forumphp:forumphp@database:5432/forumphp?sslmode=disable&charset=utf8',
        ];
        $this->dbal = DriverManager::getConnection($connectionParams);

        $this->repository = new PostgresRunningSessionRepository($this->dbal);
        $this->resetState();
    }

    private function resetState(): void
    {
        $this->dbal->executeStatement('TRUNCATE TABLE '.PostgresRunningSessionRepository::TABLE_NAME);
    }

    public function testRunningSessionIsInserted()
    {
        //When (Act)
        $session = RunningSessionFactory::create(55, 122.3, 'The shoes!', 34.5);
        $this->repository->add($session);

        //Then (Assert)
        self::thenRunningSessionTableShouldContain(55, [
            'distance' => 122.3,
            'shoes' => 'The shoes!',
            'temperature_celcius' => 34.5,
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws ExpectationFailedException
     */
    private function thenRunningSessionTableShouldContain(int $id, array $expectedArray): void
    {
        $row = $this->dbal->fetchAssociative(
            'SELECT distance, shoes, temperature_celcius '
            .' FROM RUNNING_SESSION'
            .' WHERE ID = :id', [':id' => $id]);

        self::assertIsArray($row, 'No session found with this id');

        //DB result will be strings
        $expectedArray = array_map('strval', $expectedArray);
        //Avoid failing if key order is different
        asort($row);
        asort($expectedArray);
        self::assertSame($expectedArray, $row);
    }
}
