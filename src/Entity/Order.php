<?php

namespace App\Entity;

use App\Model\IdentifiableEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="tbl_order")
 */
class Order
{
    use IdentifiableEntityTrait;
    use TimestampableEntity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", fetch="EXTRA_LAZY", mappedBy="order", cascade={"persist"})
     */
    private $items;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $buyer;

    public function __construct(User $buyer)
    {
        $this->items = new ArrayCollection();
        $this->buyer = $buyer;
    }

    public function addProduct(Product $product, int $quantity): void
    {
        foreach ($this->getItems() as $item) {
            if ($item->getProduct() === $product) {
                $item->addQuantity($quantity);
                $this->getBuyer()->increaseSpent($product->getBasePrice());

                return;
            }
        }

        $item = new OrderItem($this, $product, $quantity);
        $this->items->add($item);
        $this->getBuyer()->increaseSpent($product->getBasePrice() * $quantity);
    }

    /** @return OrderItem[] */
    public function getItems(): array
    {
        return $this->items->toArray();
    }

    public function addItem(OrderItem $item): void
    {
        $this->items->add($item);
    }

    public function getBuyer(): User
    {
        return $this->buyer;
    }
}
