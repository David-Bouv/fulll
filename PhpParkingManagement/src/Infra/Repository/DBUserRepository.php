<?php

namespace Infra\Repository;

use Domain\Entity\User;
use Domain\Repository\UserRepositoryInterface;
use Domain\ValueObject\UserId;
use PDO;

class DBUserRepository implements UserRepositoryInterface
{
    private \PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Find all users
     *
     * @return User[]
     */
    public function findAll(): array
    {
        $stmt = $this->connection->query('SELECT * FROM Users');
        $usersData = $stmt->fetchAll();

        $users = [];
        foreach ($usersData as $userData) {
            $users[] = new User(new UserId($userData['userId']), $userData['name']);
        }

        return $users;
    }

    /**
     * Find a user by their ID (string)
     *
     * @param string $id
     * @return User|null
     */
    public function findById(string $id): ?User
    {
        $stmt = $this->connection->prepare('SELECT * FROM Users WHERE userId = :userId');
        $stmt->execute(['userId' => $id]);
        $userData = $stmt->fetch();

        if ($userData) {
            return new User(new UserId($userData['userId']), $userData['name']);
        }

        return null;
    }

    /**
     * Save a user to the database
     *
     * @param User $user
     */
    public function save(User $user): void
    {
        $stmt = $this->connection->prepare('
            INSERT INTO Users (userId, name) 
            VALUES (:userId, :name) 
            ON DUPLICATE KEY UPDATE name = :name
        ');

        $stmt->execute([
            'userId' => $user->getId() ? $user->getId()->__toString() : null,
            'name' => $user->getName()
        ]);
    }
}