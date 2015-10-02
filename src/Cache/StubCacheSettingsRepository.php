<?php


namespace Oxygen\Data\Cache;

use Oxygen\Data\Behaviour\PrimaryKeyInterface;

class StubCacheSettingsRepository implements CacheSettingsRepositoryInterface {

    public function get($className) {
        return [];
    }

    public function add($class, PrimaryKeyInterface $entity) {

    }

    public function remove($class, PrimaryKeyInterface $entity) {

    }

    public function persist($withinOnFlushEvent = false) {

    }
}