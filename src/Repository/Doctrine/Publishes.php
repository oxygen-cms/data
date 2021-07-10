<?php

namespace Oxygen\Data\Repository\Doctrine;

use Oxygen\Data\Behaviour\Versionable;

trait Publishes {

    /**
     * Makes a new version of the given entity.
     *
     * @param  object  $entity the entity
     * @param  boolean $flush
     * @return void
     */
    public function makeDraftOfVersion($entity, $flush = true) {
        $version = $entity->makeNewVersion();
        $entity->unpublish();
        $this->persist($version, false, Versionable::NO_NEW_VERSION);
        $this->persist($entity, false, Versionable::NO_NEW_VERSION);
        if($flush) {
            $this->entities->flush();
        }
    }

    protected function onEntityPersisted($entity) {
        if($entity->isPublished()) {
            $this->unpublishOthers($entity);
        }
    }

    /**
     * Ensures that only one entity can be published at a time.
     *
     * @param object $entity
     * @return void
     */
    protected function unpublishOthers($entity) {
        // TODO: watch out - this isn't a perfect solution, since sometimes the Doctrine collection classes can
        //       get out of sync with the database if you do a bunch of work all at once without flushing / reloading
        //       the application
        // perhaps better to enforce this uniqueness constraint at the database level?
        if(!$entity->isHead()) {
            $head = $entity->getHead();
            if($head->isPublished()) {
                $head->unpublish();
                $this->persist($head, false, Versionable::NO_NEW_VERSION);
            }
        }

        foreach($entity->getVersions() as $version) {
            if($version !== $entity && $version->isPublished()) {
                $version->unpublish();
                $this->persist($version, false, Versionable::NO_NEW_VERSION);
            }
        }
    }

}
