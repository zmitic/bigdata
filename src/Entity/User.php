<?php

namespace App\Entity;

use App\Annotation\Counted;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="tbl_user")
 *
 * @Counted(name="user")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     * ORM\Column(type="uuid_binary_ordered_time", unique=true)
     * ORM\GeneratedValue(strategy="CUSTOM")
     * ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator"))
     */
    protected $id;

    /** @ORM\Column(type="string") */
    private $username;

    /** @ORM\Column(type="decimal", nullable=true) */
    private $spent = 0;

    public function getSpent(): float
    {
        return (float) $this->spent;
    }

    public function increaseSpent(float $spent): void
    {
        $this->spent += $spent;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}
