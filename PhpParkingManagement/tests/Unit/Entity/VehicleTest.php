<?php

namespace Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Domain\Entity\Vehicle;
use Domain\ValueObject\VehicleId;

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
}