<?php

namespace Domain\Entity;

use Domain\ValueObject\FleetId;

class Fleet
{
    private FleetId $id;
    private User $owner;
    private string $name;
    private array $vehicles = [];

    public function __construct(FleetId $id, User $owner, string $name)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->name = $name;
    }

    public function addVehicle(Vehicle $vehicle): void
    {
        if (isset($this->vehicles[$vehicle->getId()->__toString()])) {
            throw new \Exception("Vehicle already registered in this fleet.");
        }

        $this->vehicles[$vehicle->getId()->__toString()] = $vehicle;
    }

    public function getVehicle(string $vehicleId): Vehicle
    {
        if (!isset($this->vehicles[$vehicleId])) {
            throw new \Exception("Vehicle not found in the fleet.");
        }

        return $this->vehicles[$vehicleId];
    }

    public function getVehicles(): array
    {
        return $this->vehicles;
    }

    public function hasVehicle(Vehicle $vehicle): bool
    {
        if (!isset($this->vehicles[$vehicle->getId()->__toString()])) {
            throw new \Exception("Vehicle does not belong to this fleet");
        }

        return true;
    }

    public function getId(): FleetId
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }
}