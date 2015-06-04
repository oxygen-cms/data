<?php

namespace Oxygen\Data\Validation\Laravel;

use Illuminate\Validation\Factory;
use Illuminate\Validation\Validator;
use Illuminate\Support\MessageBag;
use Oxygen\Data\Validation\ValidationService;

class LaravelValidationService implements ValidationService {

    /**
     * The Validation Factory.
     *
     * @var \Illuminate\Validation\Factory
     */

    protected $factory;

    /**
     * The Laravel validator instance.
     *
     * @var \Illuminate\Validation\Validator
     */

    protected $validator;

    /**
     * Constructs the LaravelValidationService.
     *
     * @param Factory $factory
     */
    public function __construct(Factory $factory) {
        $this->factory = $factory;
    }

    /**
     * Sets the data to be validated.
     *
     * @param array $data
     * @param array $rules
     * @return mixed
     */
    public function with(array $data, array $rules) {
        $this->validator = $this->factory->make($data, $rules);
    }

    /**
     * Does the validator pass?
     *
     * @return boolean
     */
    public function passes() {
        return $this->validator->passes();
    }

    /**
     * Returns the validation errors
     *
     * @return MessageBag
     */
    public function errors() {
        return $this->validator->errors();
    }
}
