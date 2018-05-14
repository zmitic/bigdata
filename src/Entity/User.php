<?php

namespace App\Entity;

use App\Annotation\Counted;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="tbl_user")
 *
 * @Counted(name="user")
 */
class User extends BaseUser
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
}
