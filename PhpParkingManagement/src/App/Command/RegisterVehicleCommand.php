<?php

namespace App\Command;

class RegisterVehicleCommand
{
    private string $fleetId;
    private string $vehicleLicensePlate;
    private ?string $vehicleType;

    public function __construct(string $fleetId, string $vehicleLicensePlate, ?string $vehicleType)
    {
        $this->fleetId = $fleetId;
        $this->vehicleLicensePlate = $vehicleLicensePlate;
        $this->vehicleType = $vehicleType;
    }

    public function getFleetId(): string
    {
        return $this->fleetId;
    }

    public function getVehicleLicensePlate(): string
    {
        return $this->vehicleLicensePlate;
    }

    public function getVehicleType(): ?string
    {
        return $this->vehicleType;
    }
}