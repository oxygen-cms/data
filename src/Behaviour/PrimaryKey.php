<?php

namespace Oxygen\Data\Behaviour;

trait PrimaryKey {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */

    private $id;

    /**
     * Returns the ID of the entity.
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Clones the entity.
     *
     * @return void
     */
    public function __clone() {
        $this->id = null;
    }

    /**
     * We should be able to look up this entity by its ID
     *
     * @return string
     */
    public static function getRouteKeyName(): string {
        return 'id';
    }

}

