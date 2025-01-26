<?php

namespace Domain\Repository;

use Domain\Entity\Vehicle;

interface VehicleRepositoryInterface
{
    public function findById(string $vehicleId): ?Vehicle;
    public function findByPlateNumber(string $licensePlate): ?Vehicle;
    public function save(Vehicle $vehicleId): void;
}
