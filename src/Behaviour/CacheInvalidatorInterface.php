<?php


namespace Oxygen\Data\Behaviour;

/**
 * Interface CacheInvalidatorInterface
 *
 * Sets up a system for specifying how one entity's contents is dependent upon another.
 *
 * @package Oxygen\Data\Behaviour
 */
interface CacheInvalidatorInterface {

    /**
     * Lets `$this` know that all the caches of `$object` should be invalidated whenever `$this` changes.
     *
     * @param \Oxygen\Data\Behaviour\PrimaryKeyInterface $object
     */
    public function addEntityToBeInvalidated(PrimaryKeyInterface $object);

    /**
     * Lets `$this` know that all the caches of `$object` should no longer be invalidated whenever `$this` changes.
     *
     * @param \Oxygen\Data\Behaviour\PrimaryKeyInterface $object
     */
    public function removeEntityToBeInvalidated(PrimaryKeyInterface $object);

    /**
     * Returns a list of the entities whose caches will be invalidated when this entity is updated.
     *
     * @return array
     */
    public function getEntitiesToBeInvalidated();

}