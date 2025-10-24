<?php

declare(strict_types=1);

namespace AppTest\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User('test@example.com', 'Test User', 'securepassword123');
    }

    public function testInitialValues(): void
    {
        $this->assertNull($this->user->getId());
        $this->assertSame('test@example.com', $this->user->getEmail());
        $this->assertSame('Test User', $this->user->getName());
        $this->assertSame('securepassword123', $this->user->getPassword());
    }

    public function testSetAndGetEmail(): void
    {
        $email = 'test@example.com';
        $this->user->setEmail($email);
        $this->assertSame($email, $this->user->getEmail());
    }

    public function testSetAndGetName(): void
    {
        $name = 'Test User';
        $this->user->setName($name);
        $this->assertSame($name, $this->user->getName());
    }

    public function testSetAndGetPassword(): void
    {
        $password = 'securepassword123';
        $this->user->setPassword($password);
        $this->assertSame($password, $this->user->getPassword());
    }

    public function testFluentInterfaces(): void
    {
        $user = $this->user->setEmail('test@example.com')
            ->setName('Test User')
            ->setPassword('password');

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertSame('Test User', $user->getName());
        $this->assertSame('password', $user->getPassword());
    }
}
