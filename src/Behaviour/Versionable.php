<?php

namespace Oxygen\Data\Behaviour;

use Doctrine\Common\Collections\Collection;

interface Versionable {

    /**
     * Returns the versions of the entity.
     *
     * @return Collection
     */

    public function getVersions();

    /**
     * Returns true if the entity has any versions.
     *
     * @return boolean
     */

    public function hasVersions();

    /**
     * Whether the current version is the head version.
     *
     * @return boolean
     */

    public function isHead();

    /**
     * Returns the primary key of the head.
     *
     * @return integer
     */

    public function getHeadId();

    /**
     * Returns the head version.
     *
     * @return object
     */

    public function getHead();

    /**
     * Sets the head version.
     *
     * @param object $head
     * @return $this
     */

    public function setHead($head);

}