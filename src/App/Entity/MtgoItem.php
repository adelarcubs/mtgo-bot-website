<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MtgoItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MtgoItemRepository::class)]
#[ORM\Table(name: 'mtgo_items')]
class MtgoItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: CardSet::class)]
    #[ORM\JoinColumn(name: 'card_set_id', referencedColumnName: 'id', nullable: false)]
    private CardSet $cardSet;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: false)]
    private float $price = 0.0;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    private ?string $collectorNumber = null;

    public function __construct(string $name, CardSet $cardSet, float $price = 0.0, ?string $collectorNumber = null)
    {
        $this->name            = $name;
        $this->cardSet         = $cardSet;
        $this->price           = $price;
        $this->collectorNumber = $collectorNumber;
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

    public function getCardSet(): CardSet
    {
        return $this->cardSet;
    }

    public function setCardSet(CardSet $cardSet): self
    {
        $this->cardSet = $cardSet;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getSetCode(): string
    {
        return $this->cardSet->getCode();
    }

    public function getCollectorNumber(): ?string
    {
        return $this->collectorNumber;
    }

    public function setCollectorNumber(?string $collectorNumber): self
    {
        $this->collectorNumber = $collectorNumber;
        return $this;
    }
}
