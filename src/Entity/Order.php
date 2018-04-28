<?php

namespace App\Entity;

use App\Model\IdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="tbl_order")
 */
class Order
{
    use IdentifiableTrait;

    /**
     * @var Product[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="tbl_product_order")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        return $this->products->toArray();
    }

    public function addProduct(Product $product): void
    {
        if ($this->products->contains($product)) {
            return;
        }
        $this->products->add($product);
    }
}

