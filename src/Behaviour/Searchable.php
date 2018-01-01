<?php

namespace Oxygen\Data\Behaviour;

interface Searchable {

    /**
     * Returns an array of fields that will be searched.
     *
     * @return array
     */
    public static function getSearchableFields();

}

