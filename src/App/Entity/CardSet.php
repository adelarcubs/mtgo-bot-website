<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CardSetRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardSetRepository::class)]
#[ORM\Table(name: 'card_sets')]
class CardSet
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 10, unique: true, nullable: false)]
    private string $code;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $release = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $totalSetSize = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $baseSetSize = null;

    public function __construct(string $name, string $code)
    {
        $this->name      = $name;
        $this->code      = $code;
        $now             = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getRelease(): ?DateTimeImmutable
    {
        return $this->release;
    }

    public function setRelease(?DateTimeImmutable $release): self
    {
        $this->release = $release;
        return $this;
    }

    public function getTotalSetSize(): ?int
    {
        return $this->totalSetSize;
    }

    public function setTotalSetSize(?int $totalSetSize): self
    {
        $this->totalSetSize = $totalSetSize;
        return $this;
    }

    public function getBaseSetSize(): ?int
    {
        return $this->baseSetSize;
    }

    public function setBaseSetSize(?int $baseSetSize): self
    {
        $this->baseSetSize = $baseSetSize;
        return $this;
    }

    public function updateTimestamps(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
