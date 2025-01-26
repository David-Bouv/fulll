<?php

namespace Tests\Unit\Database;

use PHPUnit\Framework\TestCase;
use Infra\Database\DatabaseConnection;
use PDO;

class DatabaseConnectionTest extends TestCase
{
    public function testConstructorWithDatabaseEnabled()
    {
        // Mocked configuration to avoid actual database connection
        $config = [
            'use_database' => true,
            'db' => [
                'host' => 'localhost',  // Or '127.0.0.1'
                'dbname' => 'test_db',
                'username' => 'root',
                'password' => ''
            ]
        ];

        // Mock PDO
        $mockPdo = $this->createMock(PDO::class);

        // Simulate the connection without using an actual PDO instance
        $dbConnection = new DatabaseConnection($config, $mockPdo);

        // Verify that the database is being used and that the connection is correct
        $this->assertTrue($dbConnection->getUseDatabase());
        $this->assertSame($mockPdo, $dbConnection->getConnection());
    }

    public function testConstructorWithDatabaseDisabled()
    {
        // Configuration without database connection
        $config = [
            'use_database' => false
        ];

        // Initialize the connection without using the database
        $dbConnection = new DatabaseConnection($config);

        // Verify that the database is not used
        $this->assertFalse($dbConnection->getUseDatabase());
    }

    public function testConstructorWithInvalidDatabaseConfig()
    {
        // Mock configuration with invalid host
        $config = [
            'use_database' => true,
            'db' => [
                'host' => 'invalid_host',
                'dbname' => 'invalid_db',
                'username' => 'invalid_user',
                'password' => 'invalid_password',
            ]
        ];

        // Indicates that the exception should be thrown by the test
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Database connection error:');

        // Run the constructor without a mock, this will throw an exception
        new DatabaseConnection($config);
    }

    public function testConnectionSuccess()
    {
        // Mock configuration to use the database
        $config = [
            'use_database' => true,
            'db' => [
                'host' => 'localhost',
                'dbname' => 'test_db',
                'username' => 'root',
                'password' => ''
            ]
        ];
    
        // Mock PDO connection
        $mockPdo = $this->createMock(PDO::class);
        
        // Expect `setAttribute` to be called twice with specific arguments
        $mockPdo->expects($this->exactly(2)) // Expect two calls to setAttribute
                ->method('setAttribute')
                ->with(
                    $this->logicalOr(
                        $this->equalTo(PDO::ATTR_ERRMODE),
                        $this->equalTo(PDO::ATTR_DEFAULT_FETCH_MODE)
                    ),
                    $this->logicalOr(
                        $this->equalTo(PDO::ERRMODE_EXCEPTION),
                        $this->equalTo(PDO::FETCH_ASSOC)
                    )
                );
    
        // Create an instance of DatabaseConnection with the mock PDO
        $dbConnection = new DatabaseConnection($config, $mockPdo);
    
        // Verify that the connection was successfully established
        $this->assertInstanceOf(PDO::class, $dbConnection->getConnection());
    }

    public function testCloseConnection()
    {
        // Configuration to enable the use of the database
        $config = [
            'use_database' => true,
            'db' => [
                'host' => 'localhost',
                'dbname' => 'test_db',
                'username' => 'root',
                'password' => ''
            ]
        ];

        // Mock PDO connection
        $mockPdo = $this->createMock(PDO::class);

        // Create an instance of the DatabaseConnection class with the mock PDO
        $dbConnection = new DatabaseConnection($config, $mockPdo);

        // Close the connection
        $dbConnection->closeConnection();

        // Verify that the connection has been closed
        $this->assertNull($dbConnection->getConnection());
    }
}