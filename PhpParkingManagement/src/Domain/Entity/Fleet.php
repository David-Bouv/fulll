<?php

namespace Domain\Entity;

use Domain\ValueObject\FleetId;

class Fleet
{
    private FleetId $id;
    private User $owner;
    private string $name;
    private array $vehicles = [];

    public function __construct(FleetId $id, User $owner, string $name)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->name = $name;
    }

    public function getId(): FleetId
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }
}