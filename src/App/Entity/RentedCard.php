<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RentedCardRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

use function max;

#[ORM\Entity(repositoryClass: RentedCardRepository::class)]
#[ORM\Table(name: 'rented_cards')]
class RentedCard
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: MtgoItem::class)]
    #[ORM\JoinColumn(nullable: false)]
    private MtgoItem $mtgoItem;

    #[ORM\Column(type: 'integer')]
    private int $quantity = 0;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $updatedAt;

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
        $this->quantity  = $quantity;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function incrementQuantity(int $amount = 1): self
    {
        $this->quantity += $amount;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function decrementQuantity(int $amount = 1): self
    {
        $this->quantity  = max(0, $this->quantity - $amount);
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }
}
