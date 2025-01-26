<?php

namespace Domain\ValueObject;

class FleetId
{
    private string $id;

    public function __construct(string $id = null)
    {
        $this->id = $id ?? 'FLEET-' . time() . '-' . rand(1000, 9999);
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function equals(FleetId $vehicleId): bool
    {
        return $this->id === $vehicleId->__toString();
    }
}
