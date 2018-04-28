<?php

namespace App\Entity;

use App\Model\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ManufacturerRepository")
 * @ORM\Table(name="tbl_manufacturer")
 */
class Manufacturer
{
    use IdentifiableTrait;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}

