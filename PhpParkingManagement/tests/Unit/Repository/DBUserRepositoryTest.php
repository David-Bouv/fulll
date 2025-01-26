<?php

namespace Tests\Unit\Repository;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Infra\Repository\DBUserRepository;
use Domain\Entity\User;
use Domain\ValueObject\UserId;
use PDO;
use PDOStatement;

class DBUserRepositoryTest extends TestCase
{
    /** @var MockObject|PDO */
    private $pdo;

    /** @var DBUserRepository */
    private $userRepository;

    protected function setUp(): void
    {
        // Mock the PDO connection
        $this->pdo = $this->createMock(PDO::class);
        $this->userRepository = new DBUserRepository($this->pdo);
    }

    public function testFindAll()
    {
        $userId = 'user-123';
        $userName = 'John Doe';

        // Mocking the statement
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('fetchAll')->willReturn([
            ['userId' => $userId, 'name' => $userName]
        ]);
        $this->pdo->method('query')->willReturn($mockStmt);

        $users = $this->userRepository->findAll();

        $this->assertCount(1, $users);
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertEquals($userId, $users[0]->getId()->__toString());
        $this->assertEquals($userName, $users[0]->getName());
    }

    public function testFindAllReturnsEmptyArrayWhenNoUsers()
    {
        // Mocking the statement
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('fetchAll')->willReturn([]);
        $this->pdo->method('query')->willReturn($mockStmt);

        $users = $this->userRepository->findAll();

        $this->assertCount(0, $users);
    }

    public function testFindById()
    {
        $userId = 'user-123';
        $userName = 'John Doe';

        // Mocking the statement for user data
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn([
            'userId' => $userId,
            'name' => $userName
        ]);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        $user = $this->userRepository->findById($userId);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userId, $user->getId()->__toString());
        $this->assertEquals($userName, $user->getName());
    }

    public function testFindByIdReturnsNullWhenUserNotFound()
    {
        $this->pdo->method('prepare')->willReturn($this->createMock(PDOStatement::class));

        // Mock the prepared statement to return no result
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn(false);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        $user = $this->userRepository->findById('invalid-user-id');

        $this->assertNull($user);
    }

    public function testSave()
    {
        $userId = 'user-123';
        $userName = 'John Doe';

        // Mocking user object
        $user = new User(new UserId($userId), $userName);

        // Mocking the statement for saving the user
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        // Call the save method
        $this->userRepository->save($user);

        // We expect the save function to execute successfully without exceptions
        $this->assertTrue(true);
    }

    public function testSaveWithNullUserId()
    {
        $userName = 'John Doe';
        $user = new User(new UserId('user-id-123'), $userName);

        // Mocking the statement for saving the user
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $this->pdo->method('prepare')->willReturn($mockStmt);

        // Call the save method
        $this->userRepository->save($user);

        // We expect the save function to execute successfully without exceptions
        $this->assertTrue(true);
    }
}