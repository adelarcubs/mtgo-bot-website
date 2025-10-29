<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
#[ORM\Table(name: 'cart_item')]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'cartItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Cart $cart;

    #[ORM\ManyToOne(targetEntity: MtgoItem::class)]
    #[ORM\JoinColumn(nullable: false)]
    private MtgoItem $mtgoItem;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity;

    public function __construct(Cart $cart, MtgoItem $mtgoItem, int $quantity)
    {
        $this->cart     = $cart;
        $this->mtgoItem = $mtgoItem;
        $this->quantity = $quantity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function setCart(Cart $cart): self
    {
        $this->cart = $cart;
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
