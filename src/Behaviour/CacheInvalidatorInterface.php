<?php


namespace Oxygen\Data\Behaviour;

interface CacheInvalidatorInterface {

    public function hasEntityRegisteredForCacheInvalidation(PrimaryKeyInterface $object);
    public function addEntityForCacheInvalidation(PrimaryKeyInterface $object);
    public function removeEntityForCacheInvalidation(PrimaryKeyInterface $object);

}