<?php

namespace Tests\Unit\Repository;

use PHPUnit\Framework\TestCase;
use Domain\Entity\Vehicle;
use Domain\ValueObject\VehicleId;
use Infra\Repository\InMemoryVehicleRepository;

class InMemoryVehicleRepositoryTest extends TestCase
{
    private InMemoryVehicleRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryVehicleRepository();
    }

    public function testFindByIdWhenVehicleExists()
    {
        // Setup: Create a vehicle and add it to the repository
        $vehicleId = new VehicleId('vehicle-123');
        $vehicle = $this->createMock(Vehicle::class);
        $vehicle->method('getId')->willReturn($vehicleId);
        $this->repository->save($vehicle);

        // Test: Find the vehicle by ID
        $foundVehicle = $this->repository->findById('vehicle-123');

        $this->assertSame($vehicle, $foundVehicle);
    }

    public function testFindByIdReturnsNullWhenVehicleDoesNotExist()
    {
        // Act: Try to find a vehicle that does not exist
        $vehicle = $this->repository->findById('non-existent-vehicle');
        
        // Assert: Verify that the result is null
        $this->assertNull($vehicle);
    }

    public function testFindByPlateNumberWhenVehicleExists()
    {
        // Setup: Create a vehicle with a specific license plate and add it to the repository
        $vehicleId = new VehicleId('vehicle-123');
        $vehicle = $this->createMock(Vehicle::class);
        $vehicle->method('getLicensePlate')->willReturn('ABC123');
        $vehicle->method('getId')->willReturn($vehicleId);
        $this->repository->save($vehicle);

        // Test: Find the vehicle by license plate
        $foundVehicle = $this->repository->findByPlateNumber('ABC123');

        $this->assertSame($vehicle, $foundVehicle);
    }

    public function testFindByPlateNumberReturnsNullWhenVehicleDoesNotExist()
    {
        // Act: Try to find a vehicle with a license plate that does not exist
        $vehicle = $this->repository->findByPlateNumber('XYZ999');
        
        // Assert: Verify that the result is null
        $this->assertNull($vehicle);
    }

    public function testSaveNewVehicle()
    {
        // Setup: Create a new vehicle
        $vehicleId = new VehicleId('vehicle-123');
        $vehicle = $this->createMock(Vehicle::class);
        $vehicle->method('getId')->willReturn($vehicleId);

        // Test: Save the vehicle to the repository
        $this->repository->save($vehicle);

        // Verify: The vehicle is saved in the repository
        $savedVehicle = $this->repository->findById('vehicle-123');
        $this->assertSame($vehicle, $savedVehicle);
    }

    public function testSaveOverwritesExistingVehicle()
    {
        // Setup: Create a vehicle
        $vehicleId = new VehicleId('vehicle-123');
        $vehicle = $this->createMock(Vehicle::class);
        $vehicle->method('getId')->willReturn($vehicleId);

        // Save the first vehicle
        $this->repository->save($vehicle);

        // Create and save a new vehicle with the same ID (overwrite)
        $newVehicle = $this->createMock(Vehicle::class);
        $newVehicle->method('getId')->willReturn($vehicleId);
        $this->repository->save($newVehicle);

        // Verify: The vehicle in the repository is now the new vehicle
        $savedVehicle = $this->repository->findById('vehicle-123');
        $this->assertSame($newVehicle, $savedVehicle);
    }
}