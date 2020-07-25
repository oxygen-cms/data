<?php

namespace Oxygen\Data\Pagination;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
     * @return LengthAwarePaginator
     */
    public function make(array $items, $totalItems, $perPage);

}
