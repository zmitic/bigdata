<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

trait IdentifiableEntityTrait
{
    /**
     * @ORM\Id
     * ORM\Column(type="integer")
     * ORM\GeneratedValue()
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
