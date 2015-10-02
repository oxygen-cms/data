<?php

namespace Oxygen\Data\Cache;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Illuminate\Contracts\Events\Dispatcher;
use Oxygen\Data\Behaviour\CacheInvalidatorInterface;

class CacheInvalidationSubscriber implements EventSubscriber {

    /**
     * The config.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    protected $cacheSettings;

    /**
     * Constructs the CacheInvalidationSubscriber.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher             $events
     * @param \Oxygen\Data\Cache\CacheSettingsRepositoryInterface $cacheSettings
     */
    public function __construct(Dispatcher $events, CacheSettingsRepositoryInterface $cacheSettings) {
        $this->events = $events;
        $this->cacheSettings = $cacheSettings;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents() {
        return [
            Events::preUpdate,
            Events::preRemove
        ];
    }

    /**
     * Invalidates the cache.
     *
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function preUpdate(LifecycleEventArgs $args) {
        $this->invalidate($args->getEntityManager(), $args->getEntity());
    }

    /**
     * Invalidates the cache.
     *
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function preRemove(LifecycleEventArgs $args) {
        $this->invalidate($args->getEntityManager(), $args->getEntity());
    }

    /**
     * Invalidates the cache.
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param object                      $entity
     */
    public function invalidate(EntityManager $em, $entity) {
        $this->events->fire('oxygen.entity.cache.invalidated', [$entity]);

        // if the cache depends on any entities of a given type
        foreach($this->cacheSettings->getForEntity(get_class($entity)) as $entity) {
            $this->invalidate($em, $this->find($em, $entity));
        }

        // if the cache depends on a specific entity
        if($entity instanceof CacheInvalidatorInterface) {
            foreach($entity->getEntitiesToBeInvalidated() as $entity) {
                $this->invalidate($em, $this->find($em, $entity));
            }
        }
    }

    private function find(EntityManager $em, $entity) {
        $repo = $em->getRepository($entity['class']);
        return $repo->find($entity['id']);
    }

}