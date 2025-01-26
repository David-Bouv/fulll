<?php

namespace Tests\Unit\Repository;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Infra\Repository\DBFleetRepository;
use Domain\Entity\Fleet;
use Domain\Entity\User;
use Domain\Entity\Vehicle;
use Domain\ValueObject\FleetId;
use Domain\ValueObject\UserId;
use Domain\ValueObject\VehicleId;
use PDO;
use PDOStatement;

class DBFleetRepositoryTest extends TestCase
{
    /** @var MockObject|PDO */
    private $pdo;

    /** @var DBFleetRepository */
    private $fleetRepository;

    protected function setUp(): void
    {
        // Mock the PDO connection
        $this->pdo = $this->createMock(PDO::class);
        $this->fleetRepository = new DBFleetRepository($this->pdo);
    }

    public function testFindById()
    {
        $fleetId = 'fleet-123';
        $userId = 'user-123';
        $userName = 'John Doe';
        $fleetName = 'Fleet A';

        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn([
            'fleetId' => $fleetId,
            'userId' => $userId,
            'user_name' => $userName,
            'fleet_name' => $fleetName
        ]);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        $fleet = $this->fleetRepository->findById($fleetId);

        $this->assertInstanceOf(Fleet::class, $fleet);
        $this->assertEquals($fleetId, $fleet->getId()->__toString());
        $this->assertEquals($fleetName, $fleet->getName());
        $this->assertEquals($userId, $fleet->getOwner()->getId()->__toString());
        $this->assertEquals($userName, $fleet->getOwner()->getName());
    }

    public function testFindByIdThrowsExceptionWhenNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Fleet not found.');

        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn(false);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        $this->fleetRepository->findById('invalid-fleet-id');
    }

    public function testFindByVehicleId()
    {
        $vehicleId = 'vehicle-123';
        $fleetId = 'fleet-123';
        $userId = 'user-123';
        $userName = 'John Doe';
        $fleetName = 'Fleet A';

        // Mock the statement for the query
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn([
            'fleetId' => $fleetId,
            'userId' => $userId,
            'user_name' => $userName,
            'fleet_name' => $fleetName
        ]);

        // Configure the PDO mock to always return this statement
        $this->pdo->method('prepare')->willReturn($mockStmt);

        // Test method call
        $fleet = $this->fleetRepository->findByVehicleId($vehicleId);

        $this->assertInstanceOf(Fleet::class, $fleet);
        $this->assertEquals($fleetId, $fleet->getId()->__toString());
        $this->assertEquals($fleetName, $fleet->getName());
        $this->assertEquals($userId, $fleet->getOwner()->getId()->__toString());
        $this->assertEquals($userName, $fleet->getOwner()->getName());
    }

    public function testFindByVehicleIdThrowsExceptionWhenNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Fleet not found for vehicle.');

        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn(false);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        $this->fleetRepository->findByVehicleId('invalid-vehicle-id');
    }

    public function testSave()
    {
        $fleetId = 'fleet-123';
        $userId = 'user-123';
        $userName = 'John Doe';
        $fleetName = 'Fleet A';

        // Mocking fleet and user
        $user = new User(new UserId($userId), $userName);
        $fleet = new Fleet(new FleetId($fleetId), $user, $fleetName);

        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        // Mock vehicle
        $vehicle = $this->createMock(Vehicle::class);
        $vehicle->method('getId')->willReturn(new VehicleId('vehicle-123'));
        $fleet->addVehicle($vehicle);

        $this->fleetRepository->save($fleet);

        // We expect the save function to execute successfully without exceptions
        $this->assertTrue(true);
    }

    public function testSaveWithEmptyFleet()
    {
        $fleet = new Fleet(new FleetId('fleet-123'), new User(new UserId('user-123'), 'John Doe'), 'Fleet A');

        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        $this->fleetRepository->save($fleet);

        $this->assertTrue(true);
    }
}