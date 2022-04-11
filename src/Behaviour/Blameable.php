<?php

namespace Oxygen\Data\Behaviour;

use Oxygen\Auth\Entity\User;

interface Blameable {

    /**
     * @return User|null
     */
    public function getCreatedBy(): ?User;

    /**
     * @return User|null
     */
    public function getUpdatedBy(): ?User;

    /**
     * @param User $createdBy
     */
    public function setCreatedBy(User $createdBy);

    /**
     * @param User $updatedBy
     */
    public function setUpdatedBy(User $updatedBy);

}