<?php


namespace Oxygen\Data\Behaviour;


interface Publishable {

    const STAGE_DRAFT = 0;

    /**
     * Determines if the page is published.
     *
     * @return boolean
     */
    public function isPublished(): bool;

    /**
     * Publishes the page.
     *
     * @return $this
     */
    public function publish();

    /**
     * Unpublishes the page.
     *
     * @return $this
     */
    public function unpublish();

}
