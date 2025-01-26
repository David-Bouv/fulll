<?php

namespace Tests\Integration\Handler;

use PHPUnit\Framework\TestCase;
use App\Handler\CreateFleetHandler;
use App\Command\CreateFleetCommand;
use Domain\Repository\FleetRepositoryInterface;
use Domain\Repository\UserRepositoryInterface;
use Domain\Entity\Fleet;
use Domain\Entity\User;
use Infra\Database\DatabaseConnection;
use Infra\Repository\FactoryRepository;

class CreateFleetHandlerTest extends TestCase
{
    private DatabaseConnection $dbConnection;
    private FleetRepositoryInterface $fleetRepository;
    private UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        $config = include(__DIR__ . '/../../../config.php');
        $this->dbConnection = new DatabaseConnection($config);
        $this->fleetRepository = FactoryRepository::create($this->dbConnection, 'fleet');
        $this->userRepository = FactoryRepository::create($this->dbConnection, 'user');
        if($this->dbConnection->getUseDatabase()){
            $this->dbConnection->getConnection()->beginTransaction();
        }
    }

    protected function tearDown(): void
    {
        if($this->dbConnection->getUseDatabase()){
            $this->dbConnection->getConnection()->rollBack();
        }
    }

    public function testHandle_CreateFleet()
    {
        // Arrange
        $handler = new CreateFleetHandler($this->fleetRepository, $this->userRepository);

        $command = new CreateFleetCommand(
            userId: 'user-id-123'
        );

        // Act
        $fleetId = $handler->handle($command);

        // Assert
        $this->assertNotNull($fleetId);

        // Verify that the user has been saved
        $savedUser = $this->userRepository->findById('user-id-123');
        $this->assertInstanceOf(User::class, $savedUser);
        $this->assertEquals('user-id-123', $savedUser->getId()->__toString());

        // Verify that the fleet has been backed up
        $savedFleet = $this->fleetRepository->findById($fleetId->__toString());
        $this->assertInstanceOf(Fleet::class, $savedFleet);
        $this->assertEquals('Default Fleet', $savedFleet->getName());
        $this->assertEquals($savedUser->getId()->__toString(), $savedFleet->getOwner()->getId()->__toString());
    }
}