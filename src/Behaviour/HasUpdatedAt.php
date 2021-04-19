<?php


namespace Oxygen\Data\Behaviour;


use Carbon\Carbon;
use DateTime;

interface HasUpdatedAt {

    /**
     * Sets when the entity was updated.
     *
     * @param DateTime $updatedAt
     * @return void
     */
    public function setUpdatedAt(DateTime $updatedAt);

    /**
     * Returns when the entity was last updated.
     *
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon;

    /**
     * Specifies that the `updated_at` field should not automatically be set upon perist/update.
     *
     * Note: this is not remembered in the database at all, so only lasts whilst this PHP object is alive.
     */
    public function preserveUpdatedAt();

}
