<?php

namespace Oxygen\Data\Behaviour;

use Carbon\Carbon;
use DateTime;

trait Timestamps {

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @var \DateTime|null
     */
    private ?DateTime $createdAt = null;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     * @var \DateTime|null
     */
    private ?DateTime $updatedAt = null;

    /**
     * @var boolean
     */
    private $overrideUpdatedAt = false;

    /**
     * Specifies that the `updated_at` field should not automatically be set upon perist/update.
     *
     * Note: this is not remembered in the database at all, so only lasts whilst this PHP object is alive.
     *
     * @return void
     */
    public function preserveUpdatedAt(): void {
        $this->overrideUpdatedAt = true;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist() {
        $now = new DateTime();
        $this->createdAt = $now;
        if(!$this->overrideUpdatedAt) {
            $this->updatedAt = $now;
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate() {
        if(!$this->overrideUpdatedAt) {
            $this->updatedAt = new DateTime();
        }
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
    public function getCreatedAt(): Carbon {
        return Carbon::instance($this->createdAt);
    }

    /**
     * Returns when the entity was last updated.
     *
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon {
        return Carbon::instance($this->updatedAt);
    }

}
