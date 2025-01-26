<?php

namespace Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use Infra\Repository\DBUserRepository;
use Domain\Entity\User;
use Domain\ValueObject\UserId;
use Infra\Database\DatabaseConnection;

class DBUserRepositoryTest extends TestCase
{
    private DatabaseConnection $dbConnection;
    private DBUserRepository $userRepository;

    protected function setUp(): void
    {
        $config = include(__DIR__ . '/../../../config.php');
        $this->dbConnection = new DatabaseConnection($config);
        $this->userRepository = new DBUserRepository($this->dbConnection->getConnection());

        $this->cleanDatabase();
        $this->seedDatabase();
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
    }

    private function cleanDatabase(): void
    {
        $this->dbConnection->getConnection()->exec('DELETE FROM Users');
    }

    private function seedDatabase(): void
    {
        // Ajouter des utilisateurs de test
        $this->dbConnection->getConnection()->exec("
            INSERT INTO Users (userId, name) VALUES 
            ('user-id-123', 'Alice'),
            ('user-id-456', 'Bob')
        ");
    }

    public function testFindAll()
    {
        // Act
        $users = $this->userRepository->findAll();

        // Assert
        $this->assertCount(2, $users);
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertEquals('user-id-123', $users[0]->getId()->__toString());
        $this->assertEquals('Alice', $users[0]->getName());
    }

    public function testFindById_UserExists()
    {
        // Act
        $user = $this->userRepository->findById('user-id-123');

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('user-id-123', $user->getId()->__toString());
        $this->assertEquals('Alice', $user->getName());
    }

    public function testFindById_UserDoesNotExist()
    {
        // Act
        $user = $this->userRepository->findById('nonexistent-id');

        // Assert
        $this->assertNull($user);
    }

    public function testSave_NewUser()
    {
        // Arrange
        $newUser = new User(new UserId('user-id-789'), 'Charlie');

        // Act
        $this->userRepository->save($newUser);

        // Assert
        $savedUser = $this->userRepository->findById('user-id-789');
        $this->assertInstanceOf(User::class, $savedUser);
        $this->assertEquals('user-id-789', $savedUser->getId()->__toString());
        $this->assertEquals('Charlie', $savedUser->getName());
    }

    public function testSave_UpdateExistingUser()
    {
        // Arrange
        $existingUser = $this->userRepository->findById('user-id-123');
        $existingUser->setName('Updated Alice');

        // Act
        $this->userRepository->save($existingUser);

        // Assert
        $updatedUser = $this->userRepository->findById('user-id-123');
        $this->assertEquals('Updated Alice', $updatedUser->getName());
    }
}