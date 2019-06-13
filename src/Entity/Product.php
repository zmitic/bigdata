<?php

declare(strict_types=1);

namespace App\Entity;

use App\Annotation\Counted;
use App\Model\IdentifiableEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use function in_array;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="tbl_product", indexes={
 *     @ORM\Index(columns={"name"}),
 *     @ORM\Index(columns={"base_price"}),
 * })
 *
 * @Counted(name="product")
 */
class Product
{
    use IdentifiableEntityTrait;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Manufacturer")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $manufacturer;

    /**
     * @var ProductCategoryReference[] | ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\ProductCategoryReference", mappedBy="product", cascade={"persist"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $categoryReferences;

    /** @ORM\Column(type="decimal", nullable=true, scale=2) */
    private $basePrice = 0;

    public function __construct()
    {
        $this->categoryReferences = new ArrayCollection();
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

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?Manufacturer $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    /** @return Category[] */
    public function getCategories(): array
    {
        return array_map(function (ProductCategoryReference $reference) {
            return $reference->getCategory();
        }, $this->categoryReferences->toArray());
    }

    public function addCategory(Category $category): void
    {
        if (in_array($category, $this->getCategories(), true)) {
            return;
        }
        $category->addProduct($this);
    }

    public function removeCategory(Category $category): void
    {
        /** @var ProductCategoryReference[] $categoryReferences */
        $categoryReferences = $this->categoryReferences->toArray();
        foreach ($categoryReferences as $reference) {
            if ($reference->getCategory() === $category) {
                $category->removeProduct($this);
            }
        }
    }

    public function getBasePrice(): float
    {
        return $this->basePrice;
    }

    public function setBasePrice(?float $basePrice): void
    {
        $this->basePrice = $basePrice;
    }

    /**
     * @internal
     */
    public function addReference(ProductCategoryReference $reference): void
    {
        $this->categoryReferences->add($reference);
    }

    /**
     * @internal
     */
    public function removeReference(ProductCategoryReference $reference): void
    {
        $this->categoryReferences->removeElement($reference);
    }
}
