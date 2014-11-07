<?php

namespace Oxygen\Data\Behaviour;

use Carbon\Carbon;
use Mitch\LaravelDoctrine\Traits\Timestamps as BaseTimestamps;

trait Timestamps {

    use BaseTimestamps;

    /**
     * Returns when the entity was created.
     *
     * @return Carbon
     */

    public function getCreatedAt() {
        return new Carbon($this->createdAt->format('Y-m-d H:i:s'), $this->createdAt->getTimezone());
    }

    /**
     * Returns when the entity was last updated.
     *
     * @return Carbon
     */

    public function getUpdatedAt() {
        return new Carbon($this->updatedAt->format('Y-m-d H:i:s'), $this->updatedAt->getTimezone());
    }

}
