<?php

namespace Oxygen\Data\Cache;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Log\Writer;
use Oxygen\Data\Behaviour\CacheInvalidatorInterface;

class CacheInvalidationSubscriber implements EventSubscriber {

    /**
     * The config.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    protected $cacheSettings;

    protected $log;

    /**
     * Constructs the CacheInvalidationSubscriber.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher             $events
     * @param \Oxygen\Data\Cache\CacheSettingsRepositoryInterface $cacheSettings
     * @param \Illuminate\Log\Writer                              $log
     */
    public function __construct(Dispatcher $events, CacheSettingsRepositoryInterface $cacheSettings, Writer $log) {
        $this->events = $events;
        $this->cacheSettings = $cacheSettings;
        $this->log = $log;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents() {
        return [
            Events::postPersist,
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
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postPersist(LifecycleEventArgs $args) {
        $this->invalidate($args->getEntityManager(), $args->getEntity());
    }

    /**
     * Invalidates the cache.
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param object                      $entity
     */
    public function invalidate(EntityManager $em, $entity) {
        $this->log->info('Invalidating entity of class ' . get_class($entity) . ' with ID ' . $entity->getId());
        $this->events->fire('oxygen.entity.cache.invalidated', [$entity]);

        // if the cache depends on any entities of a given type
        foreach($this->cacheSettings->get(get_class($entity)) as $entity) {
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