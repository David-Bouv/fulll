<?php

namespace Domain\Entity;

use Domain\ValueObject\UserId;

class User
{
    private UserId $id;
    private string $name;
    private array $fleets = [];

    public function __construct(UserId $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function addFleet(Fleet $fleet)
    {
        $this->fleets[] = $fleet;
    }

    public function getFleets(): array
    {
        return $this->fleets;
    }
}