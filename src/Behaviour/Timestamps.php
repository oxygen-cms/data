<?php

namespace Oxygen\Data\Behaviour;

use Carbon\Carbon;
use DateTime;

trait Timestamps {

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\PrePersist
     */

    public function prePersist() {
        $now = new DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    /**
     * @ORM\PreUpdate
     */

    public function preUpdate() {
        $this->updatedAt = new DateTime();
    }

    /**
     * Sets when the entity was created.
     *
     * @param DateTime $createdAt
     * @return void
     */

    public function setCreatedAt(DateTime $createdAt) {
        $this->createdAt = $createdAt;
    }

    /**
     * Sets when the entity was updated.
     *
     * @param DateTime $updatedAt
     * @return void
     */

    public function setUpdatedAt(DateTime $updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Returns when the entity was created.
     *
     * @return Carbon
     */

    public function getCreatedAt() {
        return Carbon::instance($this->createdAt);
    }

    /**
     * Returns when the entity was last updated.
     *
     * @return Carbon
     */

    public function getUpdatedAt() {
        return Carbon::instance($this->updatedAt);
    }

}
