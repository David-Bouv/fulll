<?php

namespace Domain\Entity;

use Domain\ValueObject\VehicleId;

class Vehicle
{
    private VehicleId $id;
    private string $licensePlate;
    private ?string $type;

    public function __construct(VehicleId $id, string $licensePlate, ?string $type)
    {
        $this->id = $id;
        $this->licensePlate = $licensePlate;
        $this->type = $type;
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
}