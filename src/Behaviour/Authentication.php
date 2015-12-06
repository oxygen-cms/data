<?php

namespace Oxygen\Data\Behaviour;

use Doctrine\ORM\Mapping AS ORM;

trait Authentication {

    use RememberToken;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * Gets the user's password.
     *
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Sets the user's password.
     *
     * @param string $password
     * @return void
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Get the column name of the primary key.
     *
     * @return string
     */
    public function getAuthIdentifierName() {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return integer
     */
    public function getAuthIdentifier() {
        return $this->getId();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword() {
        return $this->getPassword();
    }

    /**
     * Retrieves the id field name
     *
     * @return string
     */
    public function getKeyName() {
        return 'id';
    }

}