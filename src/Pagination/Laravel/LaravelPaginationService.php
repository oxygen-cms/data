<?php

namespace Oxygen\Data\Pagination\Laravel;

use Illuminate\Http\Request;
use Illuminate\Pagination\Factory;
use Oxygen\Data\Pagination\PaginationService;

class LaravelPaginationService implements PaginationService {

    /**
     * The Pagination Factory.
     *
     * @var Factory
     */

    protected $factory;

    /**
     * The Http Request
     *
     * @var Request
     */

    protected $request;

    /**
     * Constructs the LaravelValidationService.
     *
     * @param Factory $factory
     * @param Request $request
     */
    public function __construct(Factory $factory, Request $request) {
        $this->factory = $factory;
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
     * @return object
     */
    public function make(array $items, $totalItems, $perPage) {
        return $this->factory->make($items, $totalItems, $perPage);
    }
}