<?php

namespace Oxygen\Data\Repository\Doctrine;

use Carbon\Carbon;
use Oxygen\Data\Behaviour\Versionable;

trait Versions {

    /**
     * Makes a new version of the given entity.
     *
     * @param  Versionable  $entity the entity
     * @param  boolean      $flush
     * @return object       The new version
     */
    public function makeNewVersion(Versionable $entity, $flush = true) {
        $new = clone $entity;
        $new->setHead($entity->getHead());
        $this->entities->persist($new);
        if($flush) {
            $this->entities->flush();
        }
        return $new;
    }

    /**
     * Makes an entity the head version.
     *
     * @param Versionable $entity
     * @return boolean
     */
    public function makeHeadVersion(Versionable $entity) {
        if($entity->isHead()) {
            return false;
        }

        $oldHead = $entity->getHead();

        // update references on all old versions
        $this->entities->createQueryBuilder()
            ->update($this->entityName, 'o')
            ->set('o.headVersion', ':newHead')
            ->where('o.headVersion = :oldHead')
            ->setParameter('newHead', $entity)
            ->setParameter('oldHead', $oldHead)
            ->getQuery()
            ->execute();

        // make the old head now just a sub-version
        $oldHead->setHead($entity);
        $this->persist($oldHead, false);

        // make this entity the head
        $entity->setHead(null);
        $this->persist($entity, false);

        $this->entities->flush();
        return true;
    }

    /**
     * Determines if the entity needs a new version created.
     *
     * @param $entity
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
    public function persist($entity, $flush = true, $version = 'guess') {
        $this->entities->persist($entity);

        if($version === 'new' || ($version === 'guess' && $this->needsNewVersion($entity))) {
            $this->makeNewVersion($entity, false);
            $return = true;
        } else {
            $return = false;
        }

        if($flush) {
            $this->entities->flush();
        }

        return $return;
    }

    /**
     * Clears old versions of an entity.
     *
     * @param Versionable $entity
     * @return Versionable
     */
    public function clearVersions(Versionable $entity) {
        $entity = $entity->getHead();
        $versions = $entity->getVersions();

        foreach($versions as $version) {
            $this->delete($version, false);
        }
        $versions->clear();
        $this->persist($entity, 'overwrite');

        return $entity;
    }

}
