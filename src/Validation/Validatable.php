<?php

namespace Oxygen\Data\Validation;

interface Validatable {

    /**
     * Returns an array of validation rules used to validate the model.
     *
     * @return array
     */
    public function getValidationRules();

}
