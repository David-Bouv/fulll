<?php

namespace Infra\Repository;

use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;
use Domain\ValueObject\Location;
use Domain\ValueObject\VehicleId;
use PDO;

class DBVehicleRepository implements VehicleRepositoryInterface
{
    private \PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Find a vehicle by a specific field (either ID or license plate)
     *
     * @param string $field
     * @param string $value
     * @return Vehicle
     */
    private function findVehicleByField(string $field, string $value): ?Vehicle
    {
        $stmt = $this->connection->prepare('
            SELECT v.*, l.latitude, l.longitude, l.altitude 
            FROM Vehicles v
            LEFT JOIN Locations l ON v.locationId = l.locationId
            WHERE v.' . $field . ' = :' . $field
        );
        $stmt->execute([$field => $value]);
        $vehicleData = $stmt->fetch();

        if ($vehicleData) {
            $vehicle = new Vehicle(
                new VehicleId($vehicleData['vehicleId']),
                $vehicleData['licensePlate'],
                $vehicleData['type']
            );

            if (isset($vehicleData['latitude'])
                && isset($vehicleData['longitude']) 
                && $vehicleData['latitude'] !== null
                && $vehicleData['longitude'] !== null) {

                $location = new Location(
                    (float) $vehicleData['latitude'],
                    (float) $vehicleData['longitude'],
                    isset($vehicleData['altitude']) ? (float) $vehicleData['altitude'] : null
                );
                $vehicle->setLocation($location);
            }

            return $vehicle;
        }

        return null;
    }

    /**
     * Find a vehicle by its ID
     *
     * @param VehicleId $vehicleId
     * @return Vehicle
     */
    public function findById(string $vehicleId): ?Vehicle
    {
        return $this->findVehicleByField('vehicleId', $vehicleId);
    }

    /**
     * Find a vehicle by its license plate
     *
     * @param string $licensePlate
     * @return Vehicle
     */
    public function findByPlateNumber(string $licensePlate): ?Vehicle
    {
        return $this->findVehicleByField('licensePlate', $licensePlate);
    }

    /**
     * Back up or update a vehicle
     *
     * @param Vehicle $vehicle
     */
    public function save(Vehicle $vehicle): void
    {
        // Backup of vehicle data
        $stmt = $this->connection->prepare('
            INSERT INTO Vehicles (vehicleId, licensePlate, type, locationId) 
            VALUES (:vehicleId, :licensePlate, :type, :locationId)
            ON DUPLICATE KEY UPDATE licensePlate = :licensePlate, type = :type, locationId = :locationId
        ');

        $locationId = null;
        if ($vehicle->getLocation() !== null) {
            // Save or update the associated rental
            $location = $vehicle->getLocation();
            $locationStmt = $this->connection->prepare('
                INSERT INTO Locations (latitude, longitude, altitude) 
                VALUES (:latitude, :longitude, :altitude)
            ');

            $locationStmt->execute([
                'latitude' => $location->getLatitude(),
                'longitude' => $location->getLongitude(),
                'altitude' => $location->getAltitude(),
            ]);

            // Retrieve the ID of the inserted location
            $locationId = $this->connection->lastInsertId();
        }

        // Run query to save vehicle with rental ID
        $stmt->execute([
            'vehicleId' => $vehicle->getId()->__toString(),
            'licensePlate' => $vehicle->getLicensePlate(),
            'type' => $vehicle->getType(),
            'locationId' => $locationId,
        ]);
    }
}