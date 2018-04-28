<?php

namespace App\Model;

use Ramsey\Uuid\Uuid;

trait IdentifiableTrait
{
    /**
     * @var Uuid
     * @ORM\Id
     * @ORM\Column(type="uuid_binary_ordered_time", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator"))
     */
    protected $id;

    public function getId(): Uuid
    {
        return $this->id;
    }
}
