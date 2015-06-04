<?php

namespace Oxygen\Data\Pagination;

interface PaginationService {

    /**
     * Returns the current page.
     *
     * @return integer
     */
    public function getCurrentPage();

    /**
     * Creates the Paginator instance.
     *
     * @param array $items
     * @param int   $totalItems
     * @param int   $perPage
     * @return object
     */
    public function make(array $items, $totalItems, $perPage);

}
