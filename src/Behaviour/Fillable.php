<?php

namespace Oxygen\Data\Behaviour;

use Oxygen\Data\Exception\MassAssignmentException;

trait Fillable {

    /**
     * Fills the entity from and array.
     *
     * @param array $input
     * @return void
     * @throws MassAssignmentException if the input tries to assign an not-fillable field.
     */
    public function fromArray(array $input) {
        $fillable = $this->getFillableFields();

        foreach($input as $key => $value) {
            if(in_array($key, $fillable)) {
                $this->{'set' . ucfirst($key)}($value);
            } else {
                throw new MassAssignmentException($key);
            }
        }
    }

    /**
     * Returns the fields that should be fillable.
     *
     * @return array
     */
    protected function getFillableFields() {
        return [];
    }

}

