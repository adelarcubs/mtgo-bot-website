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

    public function __construct(string $name, CardSet $cardSet)
    {
        $this->name    = $name;
        $this->cardSet = $cardSet;
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
}
