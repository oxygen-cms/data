<?php

namespace Oxygen\Data\Behaviour;

use Illuminate\Contracts\Auth\Authenticatable;

interface Blameable {

    /**
     * @return Authenticatable|null
     */
    public function getCreatedBy(): ?Authenticatable;

    /**
     * @return Authenticatable|null
     */
    public function getUpdatedBy(): ?Authenticatable;

    /**
     * @param Authenticatable $createdBy
     */
    public function setCreatedBy(Authenticatable $createdBy);

    /**
     * @param Authenticatable $updatedBy
     */
    public function setUpdatedBy(Authenticatable $updatedBy);

}