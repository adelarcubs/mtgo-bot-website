<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_item')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: MtgoItem::class)]
    #[ORM\JoinColumn(nullable: false)]
    private MtgoItem $mtgoItem;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    public function __construct(Order $order, MtgoItem $mtgoItem, int $quantity)
    {
        $this->order    = $order;
        $this->mtgoItem = $mtgoItem;
        $this->quantity = $quantity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;
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
