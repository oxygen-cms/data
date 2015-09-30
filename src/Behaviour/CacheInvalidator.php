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
        if($this->cacheInvalidationSettings == null) {
            $this->cacheInvalidationSettings = '[]';
        }
        return json_decode($this->cacheInvalidationSettings, true);
    }

    /**
     * Returns a list of the entities whose caches will be invalidated when this entity is updated.
     *
     * @return array
     */
    public function getEntitiesToBeInvalidated() {
        return $this->getCacheInvalidationSettings();
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
     * Lets `$this` know that all the caches of `$object` should be invalidated whenever `$this` changes.
     *
     * @param \Oxygen\Data\Behaviour\PrimaryKeyInterface $object
     */
    public function addEntityToBeInvalidated(PrimaryKeyInterface $object) {
        $settings = $this->getCacheInvalidationSettings();
        $info = $this->getInfo($object);
        if(array_search($info, $settings)) { return; }
        $settings[] = $info;
        $this->setCacheInvalidationSettings($settings);
    }

    /**
     * Lets `$this` know that all the caches of `$object` should no longer be invalidated whenever `$this` changes.
     *
     * @param \Oxygen\Data\Behaviour\PrimaryKeyInterface $object
     */
    public function removeEntityToBeInvalidated(PrimaryKeyInterface $object) {
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