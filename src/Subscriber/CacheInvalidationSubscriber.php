<?php

namespace Oxygen\Data\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Illuminate\Contracts\Events\Dispatcher;
use Oxygen\Data\Behaviour\CacheInvalidator;

class CacheInvalidationSubscriber implements EventSubscriber {

    /**
     * The config.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * Constructs the CacheInvalidationSubscriber.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function __construct(Dispatcher $events) {
        $this->events = $events;
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
        $this->invalidate($args->getEntity());
    }

    /**
     * Invalidates the cache.
     *
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function preRemove(LifecycleEventArgs $args) {
        $this->invalidate($args->getEntity());
    }

    /**
     * Invalidates the cache.
     *
     * @param object $entity
     */
    protected function invalidate($entity) {
        $this->events->fire('oxygen.entity.cache.invalidated', [$entity]);

        if($entity instanceof CacheInvalidator) {
            foreach($entity->getEntitiesToBeInvalidated() as $entity) {
                $this->invalidate($entity);
            }
        }
    }

}