<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

trait IdentifiableEntityTrait
{
    /**
     * var UuidInterface
     * ORM\Id
     * ORM\Column(type="uuid_binary_ordered_time", unique=true)
     * ORM\GeneratedValue(strategy="CUSTOM")
     * ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator").
     */

    /**
     * var \Ramsey\Uuid\UuidInterface.
     *

     */

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     *
     * ORM\Column(type="uuid", unique=true)
     * ORM\GeneratedValue(strategy="CUSTOM")
     * ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     *
     * ORM\Column(type="uuid_binary_ordered_time", unique=true)
     * ORM\GeneratedValue(strategy="CUSTOM")
     * ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator")
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
