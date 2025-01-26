<?php

namespace Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Domain\Entity\Vehicle;
use Domain\ValueObject\VehicleId;
use Domain\ValueObject\Location;

class VehicleTest extends TestCase
{
    private $vehicleId;
    private $vehicle;

    protected function setUp(): void
    {
        $this->vehicleId = new VehicleId('vehicle-123');
        $this->vehicle = new Vehicle($this->vehicleId, 'ABC123', 'Car');
    }

    public function testGetId()
    {
        $this->assertSame($this->vehicleId, $this->vehicle->getId());
    }

    public function testGetLicensePlate()
    {
        $this->assertSame('ABC123', $this->vehicle->getLicensePlate());
    }

    public function testSetLicensePlate()
    {
        $this->vehicle->setLicensePlate('XYZ789');
        $this->assertSame('XYZ789', $this->vehicle->getLicensePlate());
    }

    public function testGetType()
    {
        $this->assertSame('Car', $this->vehicle->getType());
    }

    public function testSetType()
    {
        $this->vehicle->setType('Truck');
        $this->assertSame('Truck', $this->vehicle->getType());
    }

    public function testParkWithLocation()
    {
        $location = $this->createMock(Location::class);
        $this->vehicle->park($location);
        
        $this->assertSame($location, $this->vehicle->getLocation());
    }

    public function testParkWithNullLocationThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Location cannot be null.");
        
        $this->vehicle->park(null);
    }

    public function testParkWithSameLocationThrowsException()
    {
        // Create a mock for the Location class
        $location = $this->createMock(Location::class);

        // Configure the mock to simulate specific latitude, longitude, and altitude
        $location->method('getLatitude')->willReturn(12.34);
        $location->method('getLongitude')->willReturn(56.78);
        $location->method('getAltitude')->willReturn(null);

        // Make the equals method return true when comparing the same location
        $location->method('equals')->willReturn(true);

        // Park the vehicle at the mocked location
        $this->vehicle->park($location);

        // Expect an exception when trying to park at the same mocked location
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Vehicle is already parked at this location.");

        // Trying to park at the same mocked location again
        $this->vehicle->park($location);
    }

    public function testGetLocationInitiallyNull()
    {
        $this->assertNull($this->vehicle->getLocation());
    }

    public function testSetLocation()
    {
        $location = $this->createMock(Location::class);
        $this->vehicle->setLocation($location);

        $this->assertSame($location, $this->vehicle->getLocation());
    }
}