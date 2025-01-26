<?php

namespace Domain\Repository;

use Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findById(string $userId): ?User;
    public function save(User $user): void;
}