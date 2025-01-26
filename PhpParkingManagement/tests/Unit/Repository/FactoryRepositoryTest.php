<?php

namespace Tests\Unit\Repository;

use PHPUnit\Framework\TestCase;
use Infra\Database\DatabaseConnection;
use Infra\Repository\FactoryRepository;
use Infra\Repository\DBUserRepository;
use Infra\Repository\DBFleetRepository;
use Infra\Repository\DBVehicleRepository;
use Infra\Repository\InMemoryUserRepository;
use Infra\Repository\InMemoryFleetRepository;
use Infra\Repository\InMemoryVehicleRepository;
use PDO;

class FactoryRepositoryTest extends TestCase
{
    public function testCreateUserRepositoryWithDatabase()
    {
        // Mock DatabaseConnection
        $mockPdo = $this->createMock(PDO::class);
        $mockDbConnection = $this->createMock(DatabaseConnection::class);

        $mockDbConnection->method('getUseDatabase')->willReturn(true);
        $mockDbConnection->method('getConnection')->willReturn($mockPdo);

        // Act
        $repository = FactoryRepository::create($mockDbConnection, 'user');

        // Assert
        $this->assertInstanceOf(DBUserRepository::class, $repository);
    }

    public function testCreateUserRepositoryWithoutDatabase()
    {
        // Mock DatabaseConnection
        $mockDbConnection = $this->createMock(DatabaseConnection::class);

        $mockDbConnection->method('getUseDatabase')->willReturn(false);

        // Act
        $repository = FactoryRepository::create($mockDbConnection, 'user');

        // Assert
        $this->assertInstanceOf(InMemoryUserRepository::class, $repository);
    }

    public function testCreateFleetRepositoryWithDatabase()
    {
        // Mock DatabaseConnection
        $mockPdo = $this->createMock(PDO::class);
        $mockDbConnection = $this->createMock(DatabaseConnection::class);

        $mockDbConnection->method('getUseDatabase')->willReturn(true);
        $mockDbConnection->method('getConnection')->willReturn($mockPdo);

        // Act
        $repository = FactoryRepository::create($mockDbConnection, 'fleet');

        // Assert
        $this->assertInstanceOf(DBFleetRepository::class, $repository);
    }

    public function testCreateFleetRepositoryWithoutDatabase()
    {
        // Mock DatabaseConnection
        $mockDbConnection = $this->createMock(DatabaseConnection::class);

        $mockDbConnection->method('getUseDatabase')->willReturn(false);

        // Act
        $repository = FactoryRepository::create($mockDbConnection, 'fleet');

        // Assert
        $this->assertInstanceOf(InMemoryFleetRepository::class, $repository);
    }

    public function testCreateVehicleRepositoryWithDatabase()
    {
        // Mock DatabaseConnection
        $mockPdo = $this->createMock(PDO::class);
        $mockDbConnection = $this->createMock(DatabaseConnection::class);

        $mockDbConnection->method('getUseDatabase')->willReturn(true);
        $mockDbConnection->method('getConnection')->willReturn($mockPdo);

        // Act
        $repository = FactoryRepository::create($mockDbConnection, 'vehicle');

        // Assert
        $this->assertInstanceOf(DBVehicleRepository::class, $repository);
    }

    public function testCreateVehicleRepositoryWithoutDatabase()
    {
        // Mock DatabaseConnection
        $mockDbConnection = $this->createMock(DatabaseConnection::class);

        $mockDbConnection->method('getUseDatabase')->willReturn(false);

        // Act
        $repository = FactoryRepository::create($mockDbConnection, 'vehicle');

        // Assert
        $this->assertInstanceOf(InMemoryVehicleRepository::class, $repository);
    }

    public function testCreateWithInvalidRepositoryType()
    {
        // Mock DatabaseConnection
        $mockDbConnection = $this->createMock(DatabaseConnection::class);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Repository type not found');

        FactoryRepository::create($mockDbConnection, 'invalid_type');
    }
}