<?php

namespace Infra\Database;

class DatabaseConnection
{
    private \PDO|null $connection;
    private bool $useDatabase;

    public function __construct(array $overrideConfig = null, \PDO $overridePdo = null)
    {
        // Path test management
        $configPath = defined('PHPUNIT_COMPOSER_INSTALL') 
            ? '/../../config.php' 
            : '/../../../config.php';
        $config = $overrideConfig ?? include(__DIR__ . $configPath);
        $this->useDatabase = $config['use_database'];
        if ($this->useDatabase) {
            try {
                // Use provided PDO mock if available, otherwise create a real PDO connection
                $this->connection = $overridePdo ?? new \PDO(
                    'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['dbname'],
                    $config['db']['username'],
                    $config['db']['password']
                );
                $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                // Throw an exception instead of dying, making it testable
                throw new \RuntimeException("Database connection error: " . $e->getMessage(), 0, $e);
            }
        }
    }

    public function getConnection(): \PDO|null
    {
        return $this->connection;
    }

    public function closeConnection(): void
    {
        $this->connection = null;
    }

    public function getUseDatabase(): bool
    {
        return $this->useDatabase;
    }
}