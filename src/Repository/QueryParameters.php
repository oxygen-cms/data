<?php

namespace Oxygen\Data\Repository;

class QueryParameters {

    const ASCENDING = 'ASC';
    
    const DESCENDING = 'DESC';

    /**
     * @var array|string $scopes an optional array of query scopes
     */
    protected $scopes;

    /**
     * @var array how to order the results
     */
    protected $orderBy;

    public function __construct(array $scopes = [], $orderByField = null, $orderByDirection = null) {
        $this->scopes = $scopes;
        if($orderByField != null) {
            $this->orderBy = [$orderByField, $orderByDirection == null ? self::ASCENDING : $orderByDirection];
        }
    }

    /**
     * Gets the scopes for the query
     *
     * @return array
     */
    public function getScopes() {
        return $this->scopes;
    }

    /**
     * Gets the order by request for the query.
     *
     * @return array
     */
    public function getOrderBy() {
        return $this->orderBy;
    }

}