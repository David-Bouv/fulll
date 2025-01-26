<?php

namespace Infra\Repository;

use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;

class InMemoryVehicleRepository implements VehicleRepositoryInterface
{
    private array $vehicules = [];

    public function findById(string $vehicleId): ?Vehicle
    {
        if (!isset($this->vehicules[$vehicleId])) {
            return null;
        }

        return $this->vehicules[$vehicleId];
    }

    public function findByPlateNumber(string $licensePlate): ?Vehicle
    {
        foreach ($this->vehicules as $vehicle) {
            if ($vehicle->getLicensePlate() === $licensePlate) {
                return $vehicle;
            }
        }

        return null;
    }

    public function save(Vehicle $vehicle): void
    {
        $this->vehicules[$vehicle->getId()->__toString()] = $vehicle;
    }
}