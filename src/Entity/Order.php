<?php

namespace App\Entity;

use App\Model\IdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="tbl_order")
 */
class Order
{
    use IdentifiableTrait;
    use TimestampableEntity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", fetch="EXTRA_LAZY", mappedBy="order", cascade={"persist"})
     */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->id = Uuid::uuid4();
    }

    /** @return OrderItem[] */
    public function getItems(): array
    {
        return $this->items->toArray();
    }

    public function addProduct(Product $product, int $quantity): void
    {
        foreach ($this->getItems() as $item) {
            if ($item->getProduct() === $product) {
                $item->addQuantity($quantity);

                return;
            }
        }

        $item = new OrderItem($this, $product, $quantity);
        $this->items->add($item);
    }

    public function addItem(OrderItem $item): void
    {
        $this->items->add($item);
    }
}

