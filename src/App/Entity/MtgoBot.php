<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MtgoBotRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MtgoBotRepository::class)]
#[ORM\Table(name: 'mtgo_bots')]
class MtgoBot
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $lastStatus = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $lastStatusTimestamp = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getLastStatus(): ?string
    {
        return $this->lastStatus;
    }

    public function setLastStatus(?string $lastStatus): self
    {
        $this->lastStatus = $lastStatus;
        return $this;
    }

    public function getLastStatusTimestamp(): ?DateTimeInterface
    {
        return $this->lastStatusTimestamp;
    }

    public function setLastStatusTimestamp(?DateTimeInterface $lastStatusTimestamp): self
    {
        $this->lastStatusTimestamp = $lastStatusTimestamp;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }
}
