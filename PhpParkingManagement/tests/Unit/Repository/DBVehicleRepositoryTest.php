<?php

namespace Tests\Unit\Repository;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Infra\Repository\DBVehicleRepository;
use Domain\Entity\Vehicle;
use Domain\ValueObject\VehicleId;
use Domain\ValueObject\Location;
use PDO;
use PDOStatement;

class DBVehicleRepositoryTest extends TestCase
{
    /** @var MockObject|PDO */
    private $pdo;

    /** @var DBVehicleRepository */
    private $vehicleRepository;

    protected function setUp(): void
    {
        // Mock the PDO connection
        $this->pdo = $this->createMock(PDO::class);
        $this->vehicleRepository = new DBVehicleRepository($this->pdo);
    }

    public function testFindById()
    {
        $vehicleId = 'vehicle-123';
        $licensePlate = 'ABC123';
        $type = 'Car';
        $latitude = 12.3456;
        $longitude = 78.9012;
        $altitude = 100.5;

        // Mocking the statement for vehicle data
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn([
            'vehicleId' => $vehicleId,
            'licensePlate' => $licensePlate,
            'type' => $type,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'altitude' => $altitude
        ]);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        $vehicle = $this->vehicleRepository->findById($vehicleId);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertEquals($vehicleId, $vehicle->getId()->__toString());
        $this->assertEquals($licensePlate, $vehicle->getLicensePlate());
        $this->assertEquals($type, $vehicle->getType());
        $this->assertInstanceOf(Location::class, $vehicle->getLocation());
        $this->assertEquals($latitude, $vehicle->getLocation()->getLatitude());
        $this->assertEquals($longitude, $vehicle->getLocation()->getLongitude());
        $this->assertEquals($altitude, $vehicle->getLocation()->getAltitude());
    }

    public function testFindByIdReturnsNullWhenVehicleNotFound()
    {
        // Mock the query preparation
        $this->pdo->method('prepare')->willReturn($this->createMock(PDOStatement::class));

        // Mock the prepared statement to return "false" when trying to recover a vehicle
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn(false);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        // We call the findById method and ensure that it returns null
        $vehicle = $this->vehicleRepository->findById('invalid-vehicle-id');
        $this->assertNull($vehicle);
    }

    public function testFindByPlateNumber()
    {
        $licensePlate = 'ABC123';
        $vehicleId = 'vehicle-123';
        $type = 'Car';
        $latitude = 12.3456;
        $longitude = 78.9012;
        $altitude = 100.5;

        // Mocking the statement for vehicle data
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn([
            'vehicleId' => $vehicleId,
            'licensePlate' => $licensePlate,
            'type' => $type,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'altitude' => $altitude
        ]);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        $vehicle = $this->vehicleRepository->findByPlateNumber($licensePlate);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertEquals($vehicleId, $vehicle->getId()->__toString());
        $this->assertEquals($licensePlate, $vehicle->getLicensePlate());
        $this->assertEquals($type, $vehicle->getType());
        $this->assertInstanceOf(Location::class, $vehicle->getLocation());
        $this->assertEquals($latitude, $vehicle->getLocation()->getLatitude());
        $this->assertEquals($longitude, $vehicle->getLocation()->getLongitude());
        $this->assertEquals($altitude, $vehicle->getLocation()->getAltitude());
    }

    public function testFindByPlateNumberReturnsNullWhenVehicleNotFound()
    {
        // Mock the query preparation
        $this->pdo->method('prepare')->willReturn($this->createMock(PDOStatement::class));

        // Mock the prepared statement to return "false" when trying to recover a vehicle
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn(false);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        // We call the findByPlateNumber method and ensure that it returns null
        $vehicle = $this->vehicleRepository->findByPlateNumber('invalid-license-plate');
        $this->assertNull($vehicle);
    }

    public function testSaveWithLocation()
    {
        $vehicleId = 'vehicle-123';
        $licensePlate = 'ABC123';
        $type = 'Car';
        $location = new Location(12.3456, 78.9012, 100.5);
        $vehicle = new Vehicle(new VehicleId($vehicleId), $licensePlate, $type);
        $vehicle->setLocation($location);

        // Mocking the statement for saving vehicle data
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        // Mocking the statement for saving location data
        $mockLocationStmt = $this->createMock(PDOStatement::class);
        $mockLocationStmt->method('execute')->willReturn(true);
        $this->pdo->method('prepare')->willReturn($mockLocationStmt);

        // Call the save method
        $this->vehicleRepository->save($vehicle);

        $this->assertTrue(true); // Ensure no exceptions are thrown during the save
    }

    public function testSaveWithoutLocation()
    {
        $vehicleId = 'vehicle-123';
        $licensePlate = 'ABC123';
        $type = 'Car';
        $vehicle = new Vehicle(new VehicleId($vehicleId), $licensePlate, $type);

        // Mocking the statement for saving vehicle data
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        // Call the save method
        $this->vehicleRepository->save($vehicle);

        $this->assertTrue(true); // Ensure no exceptions are thrown during the save
    }
}