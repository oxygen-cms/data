<?php


namespace Oxygen\Data\Cache;

interface CacheSettingsRepositoryInterface {

    public function getForEntity($className);

}