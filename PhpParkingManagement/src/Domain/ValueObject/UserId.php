<?php

namespace Domain\ValueObject;

class UserId
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function equals(UserId $userId): bool
    {
        return $this->id === $userId->__toString();
    }
}
