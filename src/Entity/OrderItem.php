<?php

declare(strict_types=1);

namespace App\Entity;

use App\Model\IdentifiableEntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="tbl_order_item")
 * @ORM\HasLifecycleCallbacks()
 */
class OrderItem
{
    use IdentifiableEntityTrait;

    /** @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="items") */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function __construct(Order $order, Product $product, int $quantity)
    {
        $this->order = $order;
        $this->product = $product;
        $this->quantity = $quantity;
    }

    /**
     * @ORM\PrePersist()
     */
    public function updateSpentMoney(): void
    {
        $buyer = $this->order->getBuyer();
        $buyer->increaseSpent($this->getProduct()->getBasePrice() * $this->quantity);
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function addQuantity(int $quantity): void
    {
        $this->quantity += $quantity;
    }
}
