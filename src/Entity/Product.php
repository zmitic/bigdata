<?php

namespace App\Entity;

use App\Annotation\Counted;
use App\Model\IdentifiableEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="tbl_product", indexes={@ORM\Index(columns={"name"})})
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
     * @ORM\OneToMany(targetEntity="App\Entity\ProductCategoryReference", mappedBy="product")
     */
    private $categoryReferences;

    public function __construct()
    {
        $this->categoryReferences = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
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
}
