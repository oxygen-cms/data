<?php

namespace Oxygen\Data\Behaviour;

use Doctrine\Common\Collections\Collection;
use Oxygen\Data\Validation\Rules\Unique;
use Oxygen\Data\Validation\ValidationService;

trait Versions {

    /**
     * Returns the versions of the entity.
     *
     * @return Collection
     */
    public function getVersions(): Collection {
        if($this->isHead()) {
            return $this->versions;
        } else {
            return $this->headVersion->getVersions();
        }
    }

    /**
     * Returns true if the entity has any versions.
     *
     * @return boolean
     */
    public function hasVersions(): bool {
        return !$this->getVersions()->isEmpty();
    }

    /**
     * Whether the current version is the head version.
     *
     * @return boolean
     */
    public function isHead(): bool {
        return $this->headVersion === null || $this->headVersion === $this;
    }

    /**
     * Returns the primary key of the head.
     *
     * @return integer
     */
    public function getHeadId(): ?int {
        return $this->isHead() ? $this->getId() : $this->headVersion->getId();
    }

    /**
     * Returns the head version.
     *
     * @return object
     */
    public function getHead() {
        return ($this->isHead()) ? $this : $this->headVersion;
    }

    /**
     * Sets the head version.
     *
     * @param object $head
     * @return $this
     */
    public function setHead($head): Versionable {
        $this->headVersion = $head;
        return $this;
    }

    /**
     * Returns a validation rule that validates this entity for uniqueness, ignoring other versions.
     *
     * @param $field
     * @return Unique
     */
    protected function getUniqueValidationRule($field): Unique {
        $unique = Unique::amongst(get_class($this))->field($field)->ignoreWithId($this->getHeadId());

        // ignore other versions of this entity
        if($this->getHeadId()) {
            $unique->addWhere('headVersion', ValidationService::NOT_EQUALS, $this->getHeadId());
        }

        return $unique;
    }

    /**
     * Adds a new version to the list of versions...
     *
     * @return Versionable
     */
    public function makeNewVersion(): Versionable {
        $version = clone $this;
        $version->setHead($this->getHead());
        $this->versions->add($version);
        return $version;
    }

}

