<?php

namespace Oxygen\Data\Behaviour;

use Doctrine\Common\Collections\Collection;

interface Versionable {

    const NO_NEW_VERSION = false;
    const GUESS_IF_NEW_VERSION_REQUIRED = 'guess';
    const ALWAYS_MAKE_NEW_VERSION = 'new';

    /**
     * Returns the versions of the entity.
     *
     * @return Collection
     */
    public function getVersions(): Collection;

    /**
     * Returns true if the entity has any versions.
     *
     * @return boolean
     */
    public function hasVersions(): bool;

    /**
     * Whether the current version is the head version.
     *
     * @return boolean
     */
    public function isHead(): bool;

    /**
     * Returns the primary key of the head.
     *
     * @return integer|null
     */
    public function getHeadId(): ?int;

    /**
     * Returns the head version.
     *
     * @return Versionable
     */
    public function getHead();

    /**
     * Sets the head version.
     *
     * @param object $head
     * @return $this
     */
    public function setHead($head): Versionable;

}
