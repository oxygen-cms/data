<?php

namespace Oxygen\Data\Repository\Doctrine;

use Carbon\Carbon;
use Doctrine\ORM\QueryBuilder;

trait Versions {

    /**
     * Makes a new version of the given entity.
     *
     * @param  object  $entity the entity
     * @param  boolean $flush
     * @return object  The new version
     */

    public function makeNewVersion($entity, $flush = true) {
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
     * @param object $entity
     * @return boolean
     */

    public function makeHeadVersion($entity) {
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

    protected function needsNewVersion($entity) {
        if(!$entity->isHead()) {
            return false;
        }

        if(!$entity->hasVersions()) {
            return true;
        }

        $oldTimestamp = $entity->getVersions()->first()->getUpdatedAt();
        $newTimestamp = Carbon::now();
        $diff = $newTimestamp->diffInHours($oldTimestamp);

        if($diff >= 24) {
            return true;
        }
    }

    /**
     * Persists an entity. Creating a new version of it if needed.
     *
     * @param object $entity
     * @param string $version
     * @return void
     */

    public function persist($entity, $version = 'guess') {
        $this->entities->persist($entity);

        if($version === 'new' || ($version === 'guess' && $this->needsNewVersion($entity))) {
            $this->makeNewVersion($entity, false);
        }

        $this->entities->flush();
    }

    /**
     * Clears old versions of an entity.
     *
     * @param object $entity
     * @return object
     */

    public function clearVersions($entity) {
        $entity = $entity->getHead();
        $versions = $entity->getVersions();

        foreach($versions as $version) {
            $this->delete($version, false);
        }
        $versions->clear();
        $this->persist($entity, 'overwrite');

        return $entity;
    }

    /**
     * Filters out non-head versions.
     *
     * @param QueryBuilder $query
     * @return QueryBuilder
     */

    protected function scopeExcludeVersions(QueryBuilder $query) {
        return $query->andWhere('o.headVersion is NULL');
    }

    /**
     * Filters everything except non-head versions.
     *
     * @param QueryBuilder $query
     * @return QueryBuilder
     */

    protected function scopeOnlyVersions(QueryBuilder $query) {
        return $query->andWhere('o.headVersion is NOT NULL');
    }

}
