<?php

namespace Oxygen\Data\Validation;

interface ValidationService {

    /**
     * Sets the data to be validated.
     *
     * @param array $data
     * @param array $rules
     * @return mixed
     */
    public function with(array $data, array $rules);

    /**
     * Does the validator pass?
     *
     * @return boolean
     */
    public function passes();

    /**
     * Returns the validation errors
     *
     * @return array
     */
    public function errors();

}
