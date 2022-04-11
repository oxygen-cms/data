<?php

namespace Oxygen\Data\Behaviour;

use Oxygen\Auth\Entity\User;

trait Blames {

    /**
     * @ORM\ManyToOne(fetch="LAZY")
     */
    private ?User $createdBy;

    /**
     * @ORM\ManyToOne(fetch="LAZY")
     */
    private ?User $updatedBy;

    /**
     * @return User|null
     */
    public function getCreatedBy(): ?User {
        return $this->createdBy;
    }

    /**
     * @return User|null
     */
    public function getUpdatedBy(): ?User {
        return $this->updatedBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy(User $createdBy): void {
        $this->createdBy = $createdBy;
    }

    /**
     * @param User $updatedBy
     */
    public function setUpdatedBy(User $updatedBy): void {
        $this->updatedBy = $updatedBy;
    }

}