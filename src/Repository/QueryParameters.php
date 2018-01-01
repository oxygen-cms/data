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

    public function __construct() {
        $this->scopes = [];
        $this->orderBy = null;
    }

    /**
     * Makes a new instance of self
     * @return QueryParameters
     */
    public static function make() {
        return new QueryParameters();
    }

    /**
     * Changes how the results are ordered.
     * @param string $field
     * @return self
     */
    public function orderBy($field, $direction = self::ASCENDING) {
        $this->orderBy = [$field, $direction];
        return $this;
    }

    /**
     * Adds a scope to the scopes array.
     * @param string $scope
     * @return self
     */
    public function addScope($scope) {
        $this->scopes[] = $scope;
        return $this;
    }

    /**
     * Adds `excludeTrashed` to the scopes array.
     * @return self
     */
    public function excludeTrashed() {
        return $this->addScope('excludeTrashed');
    }

    /**
     * Adds `excludeVersions` to the scopes array.
     * @return self
     */
    public function excludeVersions() {
        return $this->addScope('excludeVersions');
    }

    /**
     * Adds `onlyTrashed` to the scopes array.
     * @return self
     */
    public function onlyTrashed() {
        return $this->addScope('onlyTrashed');
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