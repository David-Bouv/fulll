<?php

namespace Infra\Repository;

use Domain\Entity\Fleet;
use Domain\Entity\User;
use Domain\Entity\Vehicle;
use Domain\Repository\FleetRepositoryInterface;
use Domain\ValueObject\FleetId;
use Domain\ValueObject\UserId;
use Domain\ValueObject\VehicleId;
use PDO;

class DBFleetRepository implements FleetRepositoryInterface
{
    private \PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Find a fleet by its ID (string)
     *
     * @param string $fleetId
     * @return Fleet
     */
    public function findById(string $fleetId): Fleet
    {
        $stmt = $this->connection->prepare('
            SELECT 
                f.fleetId,
                f.name AS fleet_name,
                u.userId,
                u.name AS user_name
            FROM Fleets f
            JOIN Users u ON u.userId = f.userId
            WHERE f.fleetId = :fleetId
        ');
        $stmt->execute(['fleetId' => $fleetId]);
        $fleetData = $stmt->fetch();

        if ($fleetData) {
            $fleet = new Fleet(new FleetId($fleetData['fleetId']), new User(new UserId($fleetData['userId']), $fleetData['user_name']), $fleetData['fleet_name']);
            $this->loadVehiclesForFleet($fleet);

            return $fleet;
        }

        throw new \Exception("Fleet not found.");
    }

    /**
     * Find the fleet associated with a vehicle via its ID
     *
     * @param string $vehicleId
     * @return Fleet
     */
    public function findByVehicleId(string $vehicleId): Fleet
    {
        $stmt = $this->connection->prepare('
            SELECT 
                f.fleetId,
                f.name AS fleet_name,
                u.userId,
                u.name AS user_name
            FROM Fleets f 
            JOIN FleetVehicles fv ON f.fleetId = fv.fleetId
            JOIN Users u ON u.userId = f.userId
            WHERE fv.vehicleId = :vehicleId
        ');
        $stmt->execute(['vehicleId' => $vehicleId]);
        $fleetData = $stmt->fetch();

        if ($fleetData) {
            $fleet = new Fleet(new FleetId($fleetData['fleetId']), new User(new UserId($fleetData['userId']), $fleetData['user_name']), $fleetData['fleet_name']);
            $this->loadVehiclesForFleet($fleet);

            return $fleet;
        }

        throw new \Exception("Fleet not found for vehicle.");
    }

    /**
     * Load vehicles associated with the fleet
     *
     * @param Fleet $fleet
     */
    private function loadVehiclesForFleet(Fleet $fleet): void
    {
        $stmt = $this->connection->prepare('
            SELECT v.vehicleId, v.licensePlate, v.type
            FROM Vehicles v
            JOIN FleetVehicles fv ON v.vehicleId = fv.vehicleId
            WHERE fv.fleetId = :fleetId
        ');
        $stmt->execute(['fleetId' => $fleet->getId()->__toString()]);
        $vehiclesData = $stmt->fetchAll();

        foreach ($vehiclesData as $vehicleData) {
            $vehicle = new Vehicle(
                new VehicleId($vehicleData['vehicleId']),
                $vehicleData['licensePlate'],
                $vehicleData['type']
            );
            $fleet->addVehicle($vehicle);
        }
    }

    /**
     * Back up or update a fleet
     *
     * @param Fleet $fleet
     */
    public function save(Fleet $fleet): void
    {
        $stmt = $this->connection->prepare('
            INSERT INTO Fleets (fleetId, name, userId) 
            VALUES (:fleetId, :name, :userId)
            ON DUPLICATE KEY UPDATE name = :name, userId = :userId
        ');
        $stmt->execute([                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
            'fleetId' => $fleet->getId()->__toString(),
            'name' => $fleet->getName(),
            'userId' => $fleet->getOwner()->getId()->__toString(),
        ]);

        // Save vehicles in the relationship table
        foreach ($fleet->getVehicles() as $vehicle) {
            $stmt = $this->connection->prepare('
                INSERT INTO FleetVehicles (fleetId, vehicleId)
                VALUES (:fleetId, :vehicleId)
                ON DUPLICATE KEY UPDATE vehicleId = :vehicleId
            ');
            $stmt->execute([
                'fleetId' => $fleet->getId()->__toString(),
                'vehicleId' => $vehicle->getId()->__toString(),
            ]);
        }
    }
}