<?php

declare(strict_types=1);

namespace AppTest\Entity;

use App\Entity\MtgoBot;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class MtgoBotTest extends TestCase
{
    private MtgoBot $bot;

    protected function setUp(): void
    {
        $this->bot = new MtgoBot('Test Bot');
    }

    public function testInitialValues(): void
    {
        $this->assertNull($this->bot->getId());
        $this->assertSame('Test Bot', $this->bot->getName());
        $this->assertNull($this->bot->getLastStatus());
        $this->assertNull($this->bot->getLastStatusTimestamp());
        $this->assertTrue($this->bot->isActive());
        $this->assertNull($this->bot->getDescription());
    }

    public function testSetAndGetName(): void
    {
        $name = 'Test Bot';
        $this->bot->setName($name);
        $this->assertSame($name, $this->bot->getName());
    }

    public function testSetAndGetLastStatus(): void
    {
        $status = 'online';
        $this->bot->setLastStatus($status);
        $this->assertSame($status, $this->bot->getLastStatus());

        // Test null status
        $this->bot->setLastStatus(null);
        $this->assertNull($this->bot->getLastStatus());
    }

    public function testSetAndGetLastStatusTimestamp(): void
    {
        $now = new DateTimeImmutable();
        $this->bot->setLastStatusTimestamp($now);
        $this->assertSame($now, $this->bot->getLastStatusTimestamp());

        // Test null timestamp
        $this->bot->setLastStatusTimestamp(null);
        $this->assertNull($this->bot->getLastStatusTimestamp());
    }

    public function testSetAndIsActive(): void
    {
        $this->bot->setIsActive(false);
        $this->assertFalse($this->bot->isActive());

        $this->bot->setIsActive(true);
        $this->assertTrue($this->bot->isActive());
    }

    public function testSetAndGetDescription(): void
    {
        $description = 'This is a test bot';
        $this->bot->setDescription($description);
        $this->assertSame($description, $this->bot->getDescription());

        // Test null description
        $this->bot->setDescription(null);
        $this->assertNull($this->bot->getDescription());
    }

    public function testFluentInterfaces(): void
    {
        $now = new DateTimeImmutable();
        $bot = $this->bot->setName('Test Bot')
            ->setLastStatus('online')
            ->setLastStatusTimestamp($now)
            ->setIsActive(false)
            ->setDescription('Test description');

        $this->assertInstanceOf(MtgoBot::class, $bot);
        $this->assertSame('Test Bot', $bot->getName());
        $this->assertSame('online', $bot->getLastStatus());
        $this->assertSame($now, $bot->getLastStatusTimestamp());
        $this->assertFalse($bot->isActive());
        $this->assertSame('Test description', $bot->getDescription());
    }
}
