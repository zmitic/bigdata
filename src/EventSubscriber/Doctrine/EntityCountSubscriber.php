<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\Counter;
use App\Service\EntitiesCounter\Storage;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;

class EntityCountSubscriber implements EventSubscriber
{
    /** @var Storage */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $this->inc($entity, $args->getObjectManager());
    }

    private function inc(object $entity, ObjectManager $em): void
    {
        if (!$id = $this->storage->findIdForEntity($entity)) {
            return;
        }

        $counter = $this->findCounterFor($id, $em);
        $counter->inc();
    }

    private function findCounterFor(string $id, ObjectManager $em): Counter
    {
        $counter = $em->find(Counter::class, $id);
        if (!$counter) {
            $counter = new Counter($id);
            $em->persist($counter);
        }

        return $counter;
    }
}
