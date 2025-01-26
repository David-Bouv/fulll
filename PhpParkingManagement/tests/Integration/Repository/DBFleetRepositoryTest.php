<?php

namespace Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use Infra\Repository\DBFleetRepository;
use Domain\Entity\Fleet;
use Domain\Entity\User;
use Domain\Entity\Vehicle;
use Domain\ValueObject\FleetId;
use Domain\ValueObject\UserId;
use Domain\ValueObject\VehicleId;
use Infra\Database\DatabaseConnection;
use Infra\Repository\DBVehicleRepository;

class DBFleetRepositoryTest extends TestCase
{
    private DatabaseConnection $dbConnection;
    private DBFleetRepository $fleetRepository;
    private DBVehicleRepository $vehicleRepository;

    protected function setUp(): void
    {
        $config = include(__DIR__ . '/../../../config.php');
        $this->dbConnection = new DatabaseConnection($config);
        $this->fleetRepository = new DBFleetRepository($this->dbConnection->getConnection());
        $this->vehicleRepository = new DBVehicleRepository($this->dbConnection->getConnection());

        $this->cleanDatabase();
        $this->seedDatabase();
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
    }

    private function cleanDatabase(): void
    {
        $this->dbConnection->getConnection()->exec('DELETE FROM FleetVehicles');
        $this->dbConnection->getConnection()->exec('DELETE FROM Vehicles');
        $this->dbConnection->getConnection()->exec('DELETE FROM Fleets');
        $this->dbConnection->getConnection()->exec('DELETE FROM Users');
    }

    private function seedDatabase(): void
    {
        $this->dbConnection->getConnection()->exec("
            INSERT INTO Users (userId, name) VALUES 
            ('user-id-123', 'Test User')
        ");

        $this->dbConnection->getConnection()->exec("
            INSERT INTO Fleets (fleetId, name, userId) VALUES 
            ('fleet-id-123', 'Test Fleet', 'user-id-123')
        ");

        $this->dbConnection->getConnection()->exec("
            INSERT INTO Vehicles (vehicleId, licensePlate, type) VALUES 
            ('vehicle-id-123', 'ABC-123', 'Car'),
            ('vehicle-id-456', 'DEF-456', 'Truck')
        ");

        $this->dbConnection->getConnection()->exec("
            INSERT INTO FleetVehicles (fleetId, vehicleId) VALUES 
            ('fleet-id-123', 'vehicle-id-123'),
            ('fleet-id-123', 'vehicle-id-456')
        ");
    }

    public function testFindById()
    {
        // Act
        $fleet = $this->fleetRepository->findById('fleet-id-123');

        // Assert
        $this->assertInstanceOf(Fleet::class, $fleet);
        $this->assertEquals('fleet-id-123', $fleet->getId()->__toString());
        $this->assertEquals('Test Fleet', $fleet->getName());
        $this->assertEquals('user-id-123', $fleet->getOwner()->getId()->__toString());
        $this->assertCount(2, $fleet->getVehicles());
    }

    public function testFindByVehicleId()
    {
        // Act
        $fleet = $this->fleetRepository->findByVehicleId('vehicle-id-123');

        // Assert
        $this->assertInstanceOf(Fleet::class, $fleet);
        $this->assertEquals('fleet-id-123', $fleet->getId()->__toString());
        $this->assertEquals('Test Fleet', $fleet->getName());
    }

    public function testSaveNewFleet()
    {
        // Arrange
        $user = new User(new UserId('user-id-123'), 'Test User');
        $fleet = new Fleet(new FleetId('fleet-id-456'), $user, 'New Fleet');

        $vehicle = new Vehicle(new VehicleId('vehicle-id-789'), 'GHI-789', 'SUV');
        $fleet->addVehicle($vehicle);

        // Act
        $this->vehicleRepository->save($vehicle);
        $this->fleetRepository->save($fleet);

        // Assert: Check that the fleet and its vehicles have been registered
        $savedFleet = $this->fleetRepository->findById('fleet-id-456');
        $this->assertEquals('fleet-id-456', $savedFleet->getId()->__toString());
        $this->assertEquals('New Fleet', $savedFleet->getName());
        $this->assertCount(1, $savedFleet->getVehicles());
        $this->assertEquals('vehicle-id-789', $savedFleet->getVehicles()['vehicle-id-789']->getId()->__toString());
    }

    public function testSaveExistingFleet()
    {
        // Arrange: Load an existing fleet and change its name
        $fleet = $this->fleetRepository->findById('fleet-id-123');
        $fleet->setName('Updated Fleet Name');

        $newVehicle = new Vehicle(new VehicleId('vehicle-id-789'), 'GHI-789', 'SUV');
        $fleet->addVehicle($newVehicle);

        // Act
        $this->vehicleRepository->save($newVehicle);
        $this->fleetRepository->save($fleet);

        // Assert: Verify that changes have been saved
        $updatedFleet = $this->fleetRepository->findById('fleet-id-123');
        $this->assertEquals('Updated Fleet Name', $updatedFleet->getName());
        $this->assertCount(3, $updatedFleet->getVehicles());
    }
}