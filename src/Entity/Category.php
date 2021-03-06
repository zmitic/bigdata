<?php

declare(strict_types=1);

namespace App\Entity;

use App\Annotation\Counted;
use App\Model\IdentifiableEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use function in_array;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @ORM\Table(name="tbl_category")
 *
 * @Counted(name="category")
 */
class Category
{
    use IdentifiableEntityTrait;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var ArrayCollection|ProductCategoryReference[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ProductCategoryReference", mappedBy="category", cascade={"persist"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $productReferences;

    /** @ORM\Column(type="integer") */
    private $nrOfProducts = 0;

    public function __construct()
    {
        $this->productReferences = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /** @return Product[] */
    public function getProducts(): array
    {
        return array_map(function (ProductCategoryReference $reference) {
            return $reference->getProduct();
        }, $this->productReferences->toArray());
    }

    public function addProduct(Product $product): void
    {
        if (in_array($product, $this->getProducts(), true)) {
            return;
        }
        $ref = new ProductCategoryReference($product, $this);
        $this->productReferences->add($ref);
        $product->addReference($ref);
    }

    public function removeProduct(Product $product): void
    {
        $refs = $this->productReferences;
        foreach ($refs as $ref) {
            if ($ref->getProduct() === $product) {
                $this->productReferences->removeElement($ref);
                $product->removeReference($ref);
                --$this->nrOfProducts;
            }
        }
    }

    public function getNrOfProducts(): int
    {
        return $this->nrOfProducts;
    }

    /**
     * @internal
     */
    public function incNrOfProducts(): void
    {
        ++$this->nrOfProducts;
    }
}
