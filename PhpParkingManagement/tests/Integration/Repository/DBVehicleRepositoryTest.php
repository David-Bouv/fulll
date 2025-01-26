<?php

namespace Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use Infra\Repository\DBVehicleRepository;
use Domain\Entity\Vehicle;
use Domain\ValueObject\Location;
use Domain\ValueObject\VehicleId;
use Infra\Database\DatabaseConnection;

class DBVehicleRepositoryTest extends TestCase
{
    private DatabaseConnection $dbConnection;
    private DBVehicleRepository $vehicleRepository;

    protected function setUp(): void
    {
        $config = include(__DIR__ . '/../../../config.php');
        $this->dbConnection = new DatabaseConnection($config);
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
        $this->dbConnection->getConnection()->exec('DELETE FROM Vehicles');
        $this->dbConnection->getConnection()->exec('DELETE FROM Locations');
    }

    private function seedDatabase(): void
    {
        // Ajouter des données de test pour les véhicules
        $this->dbConnection->getConnection()->exec("
            INSERT INTO Vehicles (vehicleId, licensePlate, type, locationId) VALUES 
            ('vehicle-id-123', 'ABC-123', 'Car', NULL),
            ('vehicle-id-456', 'DEF-456', 'Truck', NULL)
        ");
    }

    public function testFindById_VehicleExists()
    {
        // Act
        $vehicle = $this->vehicleRepository->findById('vehicle-id-123');

        // Assert
        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertEquals('vehicle-id-123', $vehicle->getId()->__toString());
        $this->assertEquals('ABC-123', $vehicle->getLicensePlate());
        $this->assertEquals('Car', $vehicle->getType());
    }

    public function testFindByIdReturnsNullWhenVehicleDoesNotExist()
    {
        // Act
        $vehicle = $this->vehicleRepository->findById('nonexistent-id');
        
        // Assert
        $this->assertNull($vehicle);
    }

    public function testFindByPlateNumber_VehicleExists()
    {
        // Act
        $vehicle = $this->vehicleRepository->findByPlateNumber('DEF-456');

        // Assert
        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertEquals('vehicle-id-456', $vehicle->getId()->__toString());
        $this->assertEquals('DEF-456', $vehicle->getLicensePlate());
        $this->assertEquals('Truck', $vehicle->getType());
    }

    public function testSave_NewVehicleWithoutLocation()
    {
        // Arrange
        $newVehicle = new Vehicle(new VehicleId('vehicle-id-789'), 'GHI-789', 'Bike');

        // Act
        $this->vehicleRepository->save($newVehicle);

        // Assert
        $savedVehicle = $this->vehicleRepository->findById('vehicle-id-789');
        $this->assertInstanceOf(Vehicle::class, $savedVehicle);
        $this->assertEquals('vehicle-id-789', $savedVehicle->getId()->__toString());
        $this->assertEquals('GHI-789', $savedVehicle->getLicensePlate());
        $this->assertEquals('Bike', $savedVehicle->getType());
    }

    public function testSave_NewVehicleWithLocation()
    {
        // Arrange
        $newVehicle = new Vehicle(new VehicleId('vehicle-id-890'), 'JKL-890', 'Car');
        $newVehicle->setLocation(new Location(48.8566, 2.3522, 35.0)); // Paris coordinates

        // Act
        $this->vehicleRepository->save($newVehicle);

        // Assert
        $savedVehicle = $this->vehicleRepository->findById('vehicle-id-890');
        $this->assertInstanceOf(Vehicle::class, $savedVehicle);
        $this->assertEquals('vehicle-id-890', $savedVehicle->getId()->__toString());
        $this->assertEquals('JKL-890', $savedVehicle->getLicensePlate());
        $this->assertEquals('Car', $savedVehicle->getType());

        $location = $savedVehicle->getLocation();
        $this->assertInstanceOf(Location::class, $location);
        $this->assertEquals(48.8566, $location->getLatitude());
        $this->assertEquals(2.3522, $location->getLongitude());
        $this->assertEquals(35.0, $location->getAltitude());
    }

    public function testSave_UpdateExistingVehicle()
    {
        // Arrange
        $existingVehicle = $this->vehicleRepository->findById('vehicle-id-123');
        $existingVehicle->setLicensePlate('UPDATED-123');
        $existingVehicle->setType('Updated Car');

        // Act
        $this->vehicleRepository->save($existingVehicle);

        // Assert
        $updatedVehicle = $this->vehicleRepository->findById('vehicle-id-123');
        $this->assertEquals('UPDATED-123', $updatedVehicle->getLicensePlate());
        $this->assertEquals('Updated Car', $updatedVehicle->getType());
    }
}