<?php

namespace Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Domain\Entity\User;
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
}