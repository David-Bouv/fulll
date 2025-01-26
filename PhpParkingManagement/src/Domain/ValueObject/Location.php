<?php

namespace Domain\ValueObject;

class Location
{
    private float $latitude;
    private float $longitude;
    private ?float $altitude;

    public function __construct(float $latitude, float $longitude, ?float $altitude = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->altitude = $altitude;
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

    public function equals(Location $location): bool
    {
        return $this->latitude === $location->getLatitude() &&
               $this->longitude === $location->getLongitude() &&
               $this->altitude === $location->getAltitude();
    }
}