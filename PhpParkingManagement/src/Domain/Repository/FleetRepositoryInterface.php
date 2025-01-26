<?php

namespace Domain\Repository;

use Domain\Entity\Fleet;

interface FleetRepositoryInterface
{
    public function findById(string $fleetId): Fleet;
    public function findByVehicleId(string $vehicleId): Fleet;
    public function save(Fleet $fleet): void;
}
