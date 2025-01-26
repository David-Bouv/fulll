<?php

namespace Tests\Unit\Repository;

use PHPUnit\Framework\TestCase;
use Domain\Entity\User;
use Domain\ValueObject\UserId;
use Infra\Repository\InMemoryUserRepository;

class InMemoryUserRepositoryTest extends TestCase
{
    private InMemoryUserRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryUserRepository();
    }

    public function testFindByIdWhenUserExists()
    {
        // Setup: Create a user and add it to the repository
        $userId = new UserId('user-123');
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);
        $this->repository->save($user);

        // Test: Find the user by ID
        $foundUser = $this->repository->findById('user-123');

        $this->assertSame($user, $foundUser);
    }

    public function testFindByIdWhenUserDoesNotExist()
    {
        // Test: Try to find a user that does not exist
        $foundUser = $this->repository->findById('non-existent-user');

        $this->assertNull($foundUser);
    }

    public function testSaveNewUser()
    {
        // Setup: Create a new user
        $userId = new UserId('user-123');
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        // Test: Save the user to the repository
        $this->repository->save($user);

        // Verify: The user is saved in the repository
        $savedUser = $this->repository->findById('user-123');
        $this->assertSame($user, $savedUser);
    }

    public function testSaveOverwritesExistingUser()
    {
        // Setup: Create a user
        $userId = new UserId('user-123');
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        // Save the first user
        $this->repository->save($user);

        // Create and save a new user with the same ID (overwrite)
        $newUser = $this->createMock(User::class);
        $newUser->method('getId')->willReturn($userId);
        $this->repository->save($newUser);

        // Verify: The user in the repository is now the new user
        $savedUser = $this->repository->findById('user-123');
        $this->assertSame($newUser, $savedUser);
    }
}