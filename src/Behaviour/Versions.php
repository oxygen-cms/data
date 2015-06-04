<?php

namespace Oxygen\Data\Behaviour;

use Doctrine\Common\Collections\Collection;

trait Versions {

    /**
     * Returns the versions of the entity.
     *
     * @return Collection
     */
    public function getVersions() {
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
    public function hasVersions() {
        return !$this->getVersions()->isEmpty();
    }

    /**
     * Whether the current version is the head version.
     *
     * @return boolean
     */
    public function isHead() {
        return $this->headVersion === null;
    }

    /**
     * Returns the primary key of the head.
     *
     * @return integer
     */
    public function getHeadId() {
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
    public function setHead($head) {
        $this->headVersion = $head;
        return $this;
    }

    /**
     * Returns a validation rule that validates this entity for uniqueness, ignoring other versions.
     *
     * @param $field
     * @return string
     */
    protected function getUniqueValidationRule($field) {
        $rule = 'unique:' . get_class($this) . ',' . $field . ',' . $this->getHeadId() . ',id';

        // ignore other versions of this entity
        if($this->getHeadId()) {
            $rule .= ',headVersion,!=,' . $this->getHeadId();
        }

        return $rule;
    }

}

