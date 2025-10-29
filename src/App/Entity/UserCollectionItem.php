<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserCollectionItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCollectionItemRepository::class)]
#[ORM\Table(name: 'user_collection_items')]
class UserCollectionItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: MtgoItem::class)]
    #[ORM\JoinColumn(name: 'mtgo_item_id', referencedColumnName: 'id', nullable: false)]
    private MtgoItem $mtgoItem;

    #[ORM\Column(type: 'integer')]
    private int $quantity = 0;

    public function __construct(User $user, MtgoItem $mtgoItem, int $quantity = 1)
    {
        $this->user     = $user;
        $this->mtgoItem = $mtgoItem;
        $this->quantity = $quantity;
    }

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getMtgoItem(): MtgoItem
    {
        return $this->mtgoItem;
    }

    public function setMtgoItem(MtgoItem $mtgoItem): self
    {
        $this->mtgoItem = $mtgoItem;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }
}
