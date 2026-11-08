<?php

namespace Oxygen\Data\Behaviour;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Illuminate\Auth\AuthManager;

class BlameableSubscriber implements EventSubscriber {

    private AuthManager $authManager;

    public function __construct(AuthManager $authManager) {
        $this->authManager = $authManager;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents() {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * Sets created_by and updated_by columns
     *
     * @param PrePersistEventArgs $args
     * @return void
     */
    public function prePersist(PrePersistEventArgs $args) {
        $entity = $args->getObject();
        if($entity instanceof Blameable) {
            $user = $this->authManager->guard()->user();
            $entity->setCreatedBy($user);
            $entity->setUpdatedBy($user);
        }
    }

    /**
     * Sets updated_by column
     *
     * @param PreUpdateEventArgs $args
     * @return void
     */
    public function preUpdate(PreUpdateEventArgs $args) {
        $entity = $args->getObject();
        if($entity instanceof Blameable) {
            $user = $this->authManager->guard()->user();
            $entity->setUpdatedBy($user);
        }
    }

}