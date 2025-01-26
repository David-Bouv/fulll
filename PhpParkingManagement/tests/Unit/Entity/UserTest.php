<?php

namespace Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Domain\Entity\User;
use Domain\Entity\Fleet;
use Domain\ValueObject\UserId;

class UserTest extends TestCase
{
    private $userId;
    private $user;

    protected function setUp(): void
    {
        $this->userId = new UserId('user-123');
        $this->user = new User($this->userId, 'John Doe');
    }

    public function testGetId()
    {
        $this->assertSame($this->userId, $this->user->getId());
    }

    public function testGetName()
    {
        $this->assertSame('John Doe', $this->user->getName());
    }

    public function testSetName()
    {
        $this->user->setName('Jane Doe');
        $this->assertSame('Jane Doe', $this->user->getName());
    }

    public function testAddFleet()
    {
        $fleet = $this->createMock(Fleet::class);
        $this->user->addFleet($fleet);
        
        $this->assertCount(1, $this->user->getFleets());
        $this->assertSame($fleet, $this->user->getFleets()[0]);
    }

    public function testGetFleetsReturnsEmptyArrayByDefault()
    {
        $this->assertEmpty($this->user->getFleets());
    }

    public function testAddMultipleFleets()
    {
        $fleet1 = $this->createMock(Fleet::class);
        $fleet2 = $this->createMock(Fleet::class);

        $this->user->addFleet($fleet1);
        $this->user->addFleet($fleet2);

        $fleets = $this->user->getFleets();

        $this->assertCount(2, $fleets);
        $this->assertSame($fleet1, $fleets[0]);
        $this->assertSame($fleet2, $fleets[1]);
    }
}