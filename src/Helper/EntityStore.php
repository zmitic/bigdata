<?php

declare(strict_types=1);

namespace App\Helper;

use App\Model\IdentifiableEntityTrait;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use function random_int;
use function sprintf;
use function var_dump;

class EntityStore
{
    private $storage = [];

    private $counter = [];

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param IdentifiableEntityTrait $entity
     */
    public function store($entity): void
    {
        $className = ClassUtils::getClass($entity);
        $count = $this->getCount($className);
        if ($count >= 500) {
            return;
        }
        $this->storage[$className][] = $entity->getId();
        $this->counter[$className] = $count + 1;
    }

    public function random(string $className)
    {
        $count = $this->getCount($className);
        if (!$count) {
            throw new InvalidArgumentException(sprintf('No storage for "%s".', $className));
        }
        $rand = random_int(0, $count - 1);

        $id = $this->storage[$className][$rand];

        return $this->em->getReference($className, $id);
    }

    private function getCount($className): int
    {
        return $this->counter[$className] ?? 0;
    }
}
