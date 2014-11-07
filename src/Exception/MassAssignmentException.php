<?php

namespace Oxygen\Data\Exception;

use RuntimeException;

class MassAssignmentException extends RuntimeException {

    /**
     * Constructs the MassAssignmentException.
     *
     * @param string $field
     */

    public function __construct($field) {
        parent::__construct('\'' . $field . '\' is not fillable');
    }

}