<?php

namespace Domain\Entity;

use Domain\ValueObject\VehicleId;
use Domain\ValueObject\Location;

class Vehicle
{
    private VehicleId $id;
    private string $licensePlate;
    private ?string $type;
    private ?Location $location = null;

    public function __construct(VehicleId $id, string $licensePlate, ?string $type)
    {
        $this->id = $id;
        $this->licensePlate = $licensePlate;
        $this->type = $type;
    }

    public function park(?Location $location)
    {
        if ($location === null) {
            throw new \Exception("Location cannot be null.");
        }

        if ($this->location !== null && $this->location->equals($location)) {
            throw new \Exception("Vehicle is already parked at this location.");
        }
        $this->location = $location;
    }

    public function getId(): VehicleId
    {
        return $this->id;
    }

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate)
    {
        $this->licensePlate = $licensePlate;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type)
    {
        $this->type = $type;
    }

    public function setLocation(Location $location)
    {
        $this->location = $location;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }
}