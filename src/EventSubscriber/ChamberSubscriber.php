<?php

namespace App\EventSubscriber;

use App\Entity\Chamber;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChamberSubscriber implements EventSubscriber
{
    private $housingsToUpdate = [];

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->collectHousing($args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->collectHousing($args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->collectHousing($args);
    }

    private function collectHousing(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Chamber) {
            $housing = $entity->getHousing();
            if ($housing && !in_array($housing, $this->housingsToUpdate, true)) {
                $this->housingsToUpdate[] = $housing;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->housingsToUpdate)) {
            return;
        }

        $manager = $args->getObjectManager();
        $uow = $manager->getUnitOfWork();

        foreach ($this->housingsToUpdate as $housing) {
            $housing->updateAvaibleAt();
            $housing->updatePrice();
            $uow->computeChangeSet($manager->getClassMetadata(get_class($housing)), $housing);
        }

        // Clear the array to avoid re-processing in the same flush cycle
        $this->housingsToUpdate = [];

        if (!empty($uow->getScheduledEntityUpdates())) {
            $manager->flush();
        }
    }
}
