<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CounterRepository")
 * @ORM\Table(name="tbl_entity_counter")
 */
class Counter
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="agg_count")
     */
    private $count;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function inc(): void
    {
        ++$this->count;
    }

    public function dec(): void
    {
        --$this->count;
    }
}
