<?php

namespace Oxygen\Data\Behaviour;


interface PrimaryKeyInterface {

    /**
     * Returns the ID of the entity.
     *
     * @return integer
     */
    public function getId();

}