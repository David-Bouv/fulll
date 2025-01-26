<?php

namespace Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Domain\Entity\Fleet;
use Domain\Entity\Vehicle;
use Domain\Entity\User;
use Domain\ValueObject\FleetId;
use Domain\ValueObject\VehicleId;

class FleetTest extends TestCase
{
    private $fleetId;
    private $user;
    private $fleet;

    protected function setUp(): void
    {
        $this->fleetId = new FleetId('fleet-123');
        $this->user = $this->createMock(User::class);
        $this->fleet = new Fleet($this->fleetId, $this->user, 'My Fleet');
    }

    public function testAddVehicle()
    {
        $vehicleId = 'vehicle-123';
        $vehicle = $this->createMock(Vehicle::class);
        $vehicle->method('getId')->willReturn(new VehicleId($vehicleId));
        
        $this->fleet->addVehicle($vehicle);
        
        $this->assertCount(1, $this->fleet->getVehicles());
        $this->assertSame($vehicle, $this->fleet->getVehicle($vehicleId));
    }

    public function testAddDuplicateVehicleThrowsException()
    {
        $vehicleId = 'vehicle-123';
        $vehicle = $this->createMock(Vehicle::class);
        $vehicle->method('getId')->willReturn(new VehicleId($vehicleId));
        
        $this->fleet->addVehicle($vehicle);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Vehicle already registered in this fleet.");
        
        // Trying to add the same vehicle again
        $this->fleet->addVehicle($vehicle);
    }

    public function testGetVehicleThrowsExceptionIfNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Vehicle not found in the fleet.");
        
        $this->fleet->getVehicle('non-existent-id');
    }

    public function testHasVehicleReturnsTrueIfVehicleExists()
    {
        $vehicleId = 'vehicle-123';
        $vehicle = $this->createMock(Vehicle::class);
        $vehicle->method('getId')->willReturn(new VehicleId($vehicleId));
        
        $this->fleet->addVehicle($vehicle);
        
        $this->assertTrue($this->fleet->hasVehicle($vehicle));
    }

    public function testHasVehicleThrowsExceptionIfVehicleNotFound()
    {
        $vehicle = $this->createMock(Vehicle::class);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Vehicle does not belong to this fleet");
        
        $this->fleet->hasVehicle($vehicle);
    }

    public function testGetId()
    {
        $this->assertSame($this->fleetId, $this->fleet->getId());
    }

    public function testGetOwner()
    {
        $this->assertSame($this->user, $this->fleet->getOwner());
    }

    public function testGetName()
    {
        $this->assertSame('My Fleet', $this->fleet->getName());
    }
}