<?php

namespace Oxygen\Data\Behaviour;

use BadMethodCallException;
use InvalidArgumentException;
use ReflectionClass;

trait Accessors {

    /**
     * Dynamic getters and setters.
     *
     * @param $method
     * @param $args
     * @return $this
     * @throws BadMethodCallException if the property doesn't exist
     * @throws InvalidArgumentException if a setter argument hasn't been provided
     * @throws \ReflectionException
     */
    public function __call($method, $args) {
        if(!preg_match('/(?P<accessor>set|get)(?P<property>[A-Z][a-zA-Z0-9]*)/', $method, $match) ||
           !property_exists(__CLASS__, $match['property'] = lcfirst($match['property']))
        ) {
            throw new BadMethodCallException('Property \'' . $method . '\' does not exist in class ' . __CLASS__);
        }

        $reflector = new ReflectionClass(__CLASS__);
        if($reflector->getProperty($match['property'])->isPrivate()) {
            throw new BadMethodCallException('Property \'' . $match['property'] . '\' in class \'' . __CLASS__ . '\' is private');
        }

        switch ($match['accessor']) {
            case 'get':
                return $this->{$match['property']};
            case 'set':
                if (!$args) {
                    throw new InvalidArgumentException($method . ' requires an argument value');
                }
                $this->{$match['property']} = $args[0];
                return $this;
        }
    }

    /**
     * Returns the raw attribute of the model.
     *
     * @param $attribute
     * @return mixed
     */
    public function getAttribute($attribute) {
        return $this->{$attribute};
    }

}

