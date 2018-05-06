<?php

namespace App\Entity;

use App\Annotation\Counted;
use App\Model\IdentifiableEntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ManufacturerRepository")
 * @ORM\Table(name="tbl_manufacturer")
 *
 * @Counted(name="manufacturer")
 */
class Manufacturer
{
    use IdentifiableEntityTrait;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

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
}
