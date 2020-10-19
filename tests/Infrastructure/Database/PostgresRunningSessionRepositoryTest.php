<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\RunningSession;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group integration
 */
class PostgresRunningSessionRepositoryTest extends KernelTestCase
{
    private Connection $dbal;
    private PostgresRunningSessionRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->dbal = self::$container->get('doctrine.dbal.default_connection');
        $this->repository = self::$container->get(PostgresRunningSessionRepository::class);
        $this->resetState();
    }

    private function resetState(): void
    {
        $this->dbal->executeStatement('TRUNCATE TABLE '.PostgresRunningSessionRepository::TABLE_NAME);
    }

    public function testRunningSessionIsInserted()
    {
        //When (Act)
        $session = new RunningSession(55, 122.3, 'The shoes!', 34.5);
        $this->repository->add($session);

        //Then (Assert)
        $this->thenRunningSessionTableShouldContain(55, [
            //DB result will be strings
            'distance' => '122.3',
            'shoes' => 'The shoes!',
            'temperature_celcius' => '34.5',
        ]);
    }

    private function thenRunningSessionTableShouldContain(int $id, array $expectedArray)
    {
        $row = $this->dbal->fetchAssociative(
            'SELECT distance, shoes, temperature_celcius '
            .' FROM RUNNING_SESSION'
            .' WHERE ID = :id', [':id' => $id]);

        self::assertIsArray($row, 'No session found with this id');

        //Avoid failing if key order is different
        asort($row);
        asort($expectedArray);
        self::assertSame($expectedArray, $row);
    }
}
