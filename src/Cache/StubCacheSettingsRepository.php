<?php


namespace Oxygen\Data\Cache;

class StubCacheSettingsRepository implements CacheSettingsRepositoryInterface {

    public function get($className) {
        return [];
    }
}