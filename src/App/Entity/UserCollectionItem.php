<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserCollectionItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCollectionItemRepository::class)]
class UserCollectionItem
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(nullable: false)]
        private User $user,
        #[ORM\ManyToOne(targetEntity: MtgoItem::class)]
        #[ORM\JoinColumn(nullable: false)]
        private MtgoItem $mtgoItem,
        #[ORM\Column(type: Types::INTEGER)]
        private int $quantity = 1,
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: Types::INTEGER)]
        private ?int $id = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
