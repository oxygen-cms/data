<?php

namespace Oxygen\Data\Behaviour;

use Carbon\Carbon;
use DateTime;

trait SoftDeletes {

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     * @var DateTime|null
     */
    protected ?DateTime $deletedAt;

    /**
     * Returns when the entity was deleted.
     *
     * @return Carbon
     */
    public function getDeletedAt() {
        return new Carbon($this->deletedAt->format('Y-m-d H:i:s'), $this->deletedAt->getTimezone());
    }

    /**
     * Sets when the entity is deleted.
     *
     * @param DateTime $deletedAt
     * @return $this
     */
    public function setDeletedAt(DateTime $deletedAt) {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    /**
     * Soft-deletes the model.
     *
     * @return void
     */
    public function delete() {
        $this->deletedAt = new DateTime();
    }

    /**
     * Restores the model.
     *
     * @return void
     */
    public function restore() {
        $this->deletedAt = null;
    }

    /**
     * Determines if the model is deleted.
     *
     * @return boolean
     */
    public function isDeleted() {
        return $this->deletedAt !== null &&
               new DateTime() > $this->deletedAt;
    }

}

