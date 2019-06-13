<?php

declare(strict_types=1);

namespace App\Entity;

use App\Model\IdentifiableEntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="tbl_product_category",
 *      )
 * @ORM\HasLifecycleCallbacks()
 */
class ProductCategoryReference
{
    use IdentifiableEntityTrait;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="categoryReferences", fetch="EAGER")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="productReferences", fetch="EAGER")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $category;

    public function __construct(Product $product, Category $category)
    {
        $this->product = $product;
        $this->category = $category;
    }

    /**
     * @ORM\PrePersist()
     */
    public function incOnInsert(): void
    {
        $this->getCategory()->incNrOfProducts();
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
