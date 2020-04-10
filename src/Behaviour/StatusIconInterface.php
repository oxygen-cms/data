<?php

namespace Oxygen\Data\Behaviour;

interface StatusIconInterface {
    /**
     * Retrieves the status icon for the model.
     *
     * @return string
     */
    public function getStatusIcon();
}