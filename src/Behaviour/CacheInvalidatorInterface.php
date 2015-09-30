<?php


namespace Oxygen\Data\Behaviour;

interface CacheInvalidatorInterface {

    public function addEntityToBeInvalidated(PrimaryKeyInterface $object);
    public function removeEntityToBeInvalidated(PrimaryKeyInterface $object);

    /**
     * Returns a list of the entities whose caches will be invalidated when this entity is updated.
     *
     * @return array
     */
    public function getEntitiesToBeInvalidated();

}