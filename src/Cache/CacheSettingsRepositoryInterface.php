<?php


namespace Oxygen\Data\Cache;

use Oxygen\Data\Behaviour\PrimaryKeyInterface;

interface CacheSettingsRepositoryInterface {

    public function get($className);

    public function persist($withinOnFlushEvent = false);

    public function add($class, PrimaryKeyInterface $entity);

    public function remove($class, PrimaryKeyInterface $entity);

}