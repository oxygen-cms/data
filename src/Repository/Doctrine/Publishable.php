<?php

namespace Oxygen\Data\Repository\Doctrine;

trait Publishable {

    /**
     * Makes a new version of the given entity.
     *
     * @param  object  $entity the entity
     * @param  boolean $flush
     * @return object  The new version
     */
    public function makeDraftOfVersion($entity, $flush = true) {
        $new = clone $entity;
        $new->setHead($entity->getHead());
        $new->publish();
        $this->persist($new, false);
        if($flush) {
            $this->entities->flush();
        }
    }

    /**
     * Persists an entity. Creating a new version of it if needed.
     * Ensures that only one version is published at a time.
     *
     * @param object $entity
     * @param string $version
     * @return boolean true if a new version was created
     */
    public function persist($entity, $version = 'guess') {
        $this->entities->persist($entity);
        
        if($version === 'new' || ($version === 'guess' && $this->needsNewVersion($entity))) {
            $this->makeNewVersion($entity, false);
            $return = true;
        } else {
            $return = false;
        }

        if($entity->isPublished()) {
            $this->unpublishOthers($entity);
        }

        $this->entities->flush();

        return $return;
    }

    /**
     * Ensures that only one entity can be published at a time.
     *
     * @param object $entity
     * @return void
     */

    protected function unpublishOthers($entity) {
        if(!$entity->isHead()) {
            $head = $entity->getHead();
            if($head->isPublished()) {
                $head->unpublish();
                $this->persist($head, false);
            }
        }

        foreach($entity->getVersions() as $version) {
            if($version !== $entity && $version->isPublished()) {
                $version->unpublish();
                $this->persist($version, false);
            }
        }
    }

}
