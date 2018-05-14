<?php

namespace App\Entity;

use App\Model\IdentifiableEntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="tbl_product_category")
 */
class ProductCategoryReference
{
    use IdentifiableEntityTrait;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="categoryReferences")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="productReferences")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $category;

    public function __construct(Product $product, Category $category)
    {
        $this->product = $product;
        $this->category = $category;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}
