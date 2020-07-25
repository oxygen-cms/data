<?php

namespace Oxygen\Data\Pagination\Laravel;

use Illuminate\Http\Request;
use Illuminate\Pagination\Factory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Oxygen\Data\Pagination\PaginationService;

class LaravelPaginationService implements PaginationService {

    /**
     * The Http Request
     *
     * @var Request
     */
    protected $request;

    /**
     * Constructs the LaravelValidationService.
     *
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * Returns the current page.
     *
     * @return integer
     */
    public function getCurrentPage() {
        return $this->request->input('page', 1);
    }

    /**
     * Creates the Paginator instance.
     *
     * @param array $items
     * @param int   $totalItems
     * @param int   $perPage
     * @return LengthAwarePaginator
     */
    public function make(array $items, $totalItems, $perPage) {
        return new LengthAwarePaginator($items, $totalItems, $perPage);
    }
}