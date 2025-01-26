<?php

namespace App\Command;

class LocalizeVehicleCommand
{
    private string $fleetId;
    private string $vehiclePlateNumber;
    private float $latitude;
    private float $longitude;
    private ?float $altitude;

    public function __construct(string $fleetId, string $vehiclePlateNumber, float $latitude, float $longitude, ?float $altitude)
    {
        $this->fleetId = $fleetId;
        $this->vehiclePlateNumber = $vehiclePlateNumber;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->altitude = $altitude;
    }

    public function getFleetId(): string
    {
        return $this->fleetId;
    }

    public function getVehiclePlateNumber(): string
    {
        return $this->vehiclePlateNumber;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getAltitude(): ?float
    {
        return $this->altitude;
    }
}