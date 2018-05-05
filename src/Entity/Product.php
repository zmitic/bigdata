<?php

namespace App\Entity;

use App\Model\IdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="tbl_product")
 */
class Product
{
    use IdentifiableTrait;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var Manufacturer|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Manufacturer")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $manufacturer;

    /**
     * @var Category[] | ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="products", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="tbl_category_product")
     */
    private $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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

    /**
     * @return Category[]
     */
    public function getCategories(): array
    {
        return $this->categories->toArray();
    }

    public function addCategory(Category $category): void
    {
        if ($this->categories->contains($category)) {
            return;
        }
        $this->categories->add($category);
        $category->addProduct($this);
    }

    public function removeCategory(Category $category): void
    {
        if (!$this->categories->contains($category)) {
            return;
        }
        $this->categories->removeElement($category);
        $category->removeProduct($this);
    }
}
