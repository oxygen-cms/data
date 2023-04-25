<?php

namespace Oxygen\Data\Behaviour;

trait Publishes {

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $stage;

    /**
     * @return int
     */
    public function getStage(): int
    {
        return $this->stage;
    }

    /**
     * @param int $stage
     * @return void
     */
    public function setStage(int $stage)
    {
        $this->stage = $stage;
    }

    /**
     * Determines if the page is published.
     *
     * @return boolean
     */
    public function isPublished(): bool {
        return $this->stage == self::STAGE_PUBLISHED;
    }

    /**
     * Publishes the page.
     *
     * @return $this
     */
    public function publish() {
        $this->stage = self::STAGE_PUBLISHED;
        return $this;
    }

    /**
     * Unpublishes the page.
     *
     * @return $this
     */
    public function unpublish() {
        $this->stage = self::STAGE_DRAFT;
        return $this;
    }

}


