<?php

namespace Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Domain\Entity\Fleet;
use Domain\Entity\User;
use Domain\ValueObject\FleetId;

class FleetTest extends TestCase
{
    private $fleetId;
    private $user;
    private $fleet;

    protected function setUp(): void
    {
        $this->fleetId = new FleetId('fleet-123');
        $this->user = $this->createMock(User::class);
        $this->fleet = new Fleet($this->fleetId, $this->user, 'My Fleet');
    }

    public function testGetId()
    {
        $this->assertSame($this->fleetId, $this->fleet->getId());
    }

    public function testGetOwner()
    {
        $this->assertSame($this->user, $this->fleet->getOwner());
    }

    public function testGetName()
    {
        $this->assertSame('My Fleet', $this->fleet->getName());
    }
}