<?php

namespace App\EventSubscriber;

use App\Entity\Tenant;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class TenantAgeSubscriber implements EventSubscriber
{
    private $tenantsToUpdate = [];

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->collectTenants($args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->collectTenants($args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->collectTenants($args);
    }

    private function collectTenants(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Tenant) {
            $tenant = $entity;
            if (!in_array($tenant, $this->tenantsToUpdate, true)) {
                $this->tenantsToUpdate[] = $tenant;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->tenantsToUpdate)) {
            return;
        }

        $manager = $args->getObjectManager();
        $uow = $manager->getUnitOfWork();

        foreach ($this->tenantsToUpdate as $tenant) {
            /* @var Tenant $tenant */
            $user = $tenant->getUser();
            $now = new \DateTime();
            $age = $now->diff($user->getDateOfBirth())->y;
            $tenant->setAge($age);

            $uow->computeChangeSet($manager->getClassMetadata(get_class($tenant)), $tenant);
        }

        // Clear the array to avoid re-processing in the same flush cycle
        $this->tenantsToUpdate = [];

        if (!empty($uow->getScheduledEntityUpdates())) {
            $manager->flush();
        }
    }
}
