<?php

namespace Infra\Repository;

use Domain\Entity\Fleet;
use Domain\Repository\FleetRepositoryInterface;

class InMemoryFleetRepository implements FleetRepositoryInterface
{
    private array $fleets = [];

    public function findById(string $fleetId): Fleet
    {
        if (!isset($this->fleets[$fleetId])) {
            throw new \Exception("Fleet not found.");
        }

        return $this->fleets[$fleetId];
    }

    public function save(Fleet $fleet): void
    {
        $this->fleets[$fleet->getId()->__toString()] = $fleet;
    }
}