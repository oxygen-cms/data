<?php

namespace Oxygen\Data\Behaviour;


trait CacheInvalidator {

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $cacheInvalidationSettings;

    /**
     * Returns the cache invalidation settings.
     *
     * @return array
     */
    public function getCacheInvalidationSettings() {
        return json_decode($this->cacheInvalidationSettings, true);
    }

    /**
     * Sets the cache invalidation settings.
     *
     * @param  array|string $settings
     * @return $this
     */
    public function setCacheInvalidationSettings($settings) {
        $this->cacheInvalidationSettings = is_string($settings) ? $settings : json_encode($settings, JSON_PRETTY_PRINT);
        return $this;
    }

    /**
     * Determines if `$object` has been registered to be invalidated when `$this` changes.
     *
     * @param \Oxygen\Data\Behaviour\PrimaryKeyInterface $object
     * @return bool
     */
    public function hasEntityRegisteredForCacheInvalidation(PrimaryKeyInterface $object) {
        $settings = $this->getCacheInvalidationSettings();
        return array_search(['id' => $object->getId(), 'class' => get_class($object)], $settings, true);
    }

    /**
     * Lets `$this` know that all the caches of `$object` should be invalidated whenever `$this` changes.
     *
     * @param \Oxygen\Data\Behaviour\PrimaryKeyInterface $object
     */
    public function addEntityForCacheInvalidation(PrimaryKeyInterface $object) {
        $settings = $this->getCacheInvalidationSettings();
        $info = $this->getInfo($object);
        if(array_search($info, $settings, true)) { return; }
        $settings[] = $info;
        $this->setCacheInvalidationSettings($settings);
    }

    /**
     * Lets `$this` know that all the caches of `$object` should no longer be invalidated whenever `$this` changes.
     *
     * @param \Oxygen\Data\Behaviour\PrimaryKeyInterface $object
     */
    public function removeEntityForCacheInvalidation(PrimaryKeyInterface $object) {
        $settings = $this->getCacheInvalidationSettings();
        $info = $this->getInfo($object);
        $settings = array_filter($settings, function($value) use($info) {
            return $value != $info;
        });
        $this->setCacheInvalidationSettings($settings);
    }

    private function getInfo(PrimaryKeyInterface $object) {
        return ['id' => $object->getId(), 'class' => get_class($object)];
    }


}