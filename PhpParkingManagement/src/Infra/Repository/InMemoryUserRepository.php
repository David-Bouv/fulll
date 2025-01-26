<?php

namespace Infra\Repository;

use Domain\Entity\User;
use Domain\Repository\UserRepositoryInterface;

class InMemoryUserRepository implements UserRepositoryInterface
{
    private array $users = [];

    public function findById(string $userId): ?User
    {
        return $this->users[$userId] ?? null;
    }

    public function save(User $user): void
    {
        $this->users[$user->getId()->__toString()] = $user;
    }
}