<?php

namespace Oxygen\Data\Validation;

use Illuminate\Support\MessageBag;

interface ValidationService {

    public const NULL = 'NULL';
    public const NOT_NULL = 'NOT_NULL';
    public const EQUALS = '=';
    public const NOT_EQUALS = '!=';

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
     * @return MessageBag
     */
    public function errors();

}
