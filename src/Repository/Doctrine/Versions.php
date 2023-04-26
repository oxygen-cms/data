<?php

namespace Oxygen\Data\Repository\Doctrine;

use Carbon\Carbon;
use Oxygen\Data\Behaviour\HasUpdatedAt;
use Oxygen\Data\Behaviour\Versionable;
use phpDocumentor\Reflection\DocBlock\Tags\Version;
use Webmozart\Assert\Assert;

trait Versions {

    /**
     * Makes a new version of the given entity.
     *
     * @param  Versionable  $entity the entity
     * @param  boolean      $flush
     * @return object       The new version
     */
    public function makeNewVersion(Versionable $entity, $flush = true) {
        $version = $entity->makeNewVersion();
        $this->persist($version, $flush, Versionable::NO_NEW_VERSION);
        return $version;
    }

    /**
     * Makes an entity the head version.
     *
     * @param Versionable $entity
     * @return boolean
     */
    public function makeHeadVersion(Versionable $entity): bool {
        $fillable = $entity->getFillableFields();

        $oldHead = $entity->getHead();
        if($oldHead === $entity) {
            return true;
        }

        // preserve updated_at values.
        if($entity instanceof HasUpdatedAt) {
            $oldUpdatedAt = $oldHead->getUpdatedAt();
            $entityUpdatedAt = $entity->getUpdatedAt();
        }

        $reflect = new \ReflectionClass($entity);
        foreach($reflect->getProperties() as $prop) {
            if(in_array($prop->getName(), $fillable)) {
                $prop->setAccessible(true);
                // switch around the properties
                $oldValue = $prop->getValue($entity);
                $prop->setValue($entity, $prop->getValue($oldHead));
                $prop->setValue($oldHead, $oldValue);
            }
        }

        if($entity instanceof HasUpdatedAt) {
            $oldHead->setUpdatedAt($entityUpdatedAt);
            $entity->setUpdatedAt($oldUpdatedAt);
            $entity->preserveUpdatedAt();
            $oldHead->preserveUpdatedAt();
        }

        $this->persist($entity, false, Versionable::NO_NEW_VERSION);
        $this->persist($oldHead, false, Versionable::NO_NEW_VERSION);

        $this->entities->flush();
        return true;
    }

    /**
     * Determines if the entity needs a new version created.
     *
     * @param Versionable $entity
     * @return boolean
     */
    protected function needsNewVersion(Versionable $entity): bool {
        if(!$entity->isHead()) {
            return false;
        }

        if(!$entity->hasVersions()) {
            return true;
        }

        $oldTimestamp = $entity->getVersions()->first()->getUpdatedAt();
        $newTimestamp = Carbon::now();
        $diff = $newTimestamp->diffInHours($oldTimestamp);

        return $diff >= 24;
    }

    /**
     * Persists an entity. Creating a new version of it if needed.
     *
     * @param object $entity
     * @param boolean $flush
     * @param string $version
     * @return boolean true if a new version was created
     */
    public function persist($entity, $flush = true, $version = Versionable::GUESS_IF_NEW_VERSION_REQUIRED): bool {
        Assert::isInstanceOf($entity, Versionable::class);
        if($version === Versionable::ALWAYS_MAKE_NEW_VERSION ||
            ($version === Versionable::GUESS_IF_NEW_VERSION_REQUIRED && $this->needsNewVersion($entity))) {
            $this->makeNewVersion($entity, false);
            $return = true;
        } else {
            $return = false;
        }

        $this->entities->persist($entity);
        $this->onEntityPersisted($entity);

        if($flush) {
            $this->entities->flush();
        }

        return $return;
    }

    protected function onEntityPersisted($entity) {

    }

    /**
     * Clears old versions of an entity.
     *
     * @param Versionable $entity
     * @return Versionable
     */
    public function clearVersions(Versionable $entity): Versionable {
        $entity = $entity->getHead();
        $versions = $entity->getVersions();

        foreach($versions as $version) {
            $this->delete($version, false);
        }
        $versions->clear();
        $this->persist($entity, true, Versionable::NO_NEW_VERSION);

        return $entity;
    }

}
