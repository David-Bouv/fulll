<?php

namespace Infra\Repository;

use Infra\Database\DatabaseConnection;

class FactoryRepository
{
    public static function create(DatabaseConnection $dbconnexion, string $repositoryType)
    {
        switch ($repositoryType) {
            case 'user':
                return $dbconnexion->getUseDatabase() ? new DBUserRepository($dbconnexion->getConnection()) : new InMemoryUserRepository();
            case 'fleet':
                return $dbconnexion->getUseDatabase() ? new DBFleetRepository($dbconnexion->getConnection()) : new InMemoryFleetRepository();
            case 'vehicle':
                return $dbconnexion->getUseDatabase() ? new DBVehicleRepository($dbconnexion->getConnection()) : new InMemoryVehicleRepository();
            default:
                throw new \Exception("Repository type not found");
        }
    }
}