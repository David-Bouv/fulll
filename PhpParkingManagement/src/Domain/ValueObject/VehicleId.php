<?php

namespace Domain\ValueObject;

class VehicleId
{
    private string $id;

    public function __construct(string $id = null)
    {
        $this->id = $id ?? 'VEHICLE-' . time() . '-' . rand(1000, 9999);
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function equals(VehicleId $vehicleId): bool
    {
        return $this->id === $vehicleId->__toString();
    }
}
