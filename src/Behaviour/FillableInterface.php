<?php


namespace Oxygen\Data\Behaviour;


use Oxygen\Data\Exception\MassAssignmentException;

interface FillableInterface {

    /**
     * Fills the entity from an array.
     *
     * @param array $input
     * @return void
     * @throws MassAssignmentException if the input tries to assign an not-fillable field.
     */
    public function fromArray(array $input);

    /**
     * Returns the fields that should be fillable.
     *
     * @return array
     */
    public function getFillableFields(): array;

}
