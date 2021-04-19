<?php

namespace Oxygen\Data\Repository;

class QueryParameters {

    const ASCENDING = 'ASC';

    const DESCENDING = 'DESC';

    /**
     * @var array|string $clauses an optional array of query scopes
     */
    protected $clauses;

    /**
     * @var array|null how to order the results
     */
    protected $orderBy;

    /**
     * QueryParameters constructor.
     * @param array $scopes
     */
    public function __construct($scopes = []) {
        $this->clauses = $scopes;
        $this->orderBy = null;
    }

    /**
     * Makes a new instance of self
     * @return QueryParameters
     */
    public static function make(): QueryParameters {
        return new QueryParameters();
    }

    /**
     * Changes how the results are ordered.
     * @param string $field
     * @param string $direction
     * @return self
     */
    public function orderBy(string $field, $direction = self::ASCENDING) {
        $this->orderBy = [$field, $direction];
        return $this;
    }

    /**
     * @param QueryClauseInterface $clause
     * @return $this
     */
    public function addClause(QueryClauseInterface $clause): QueryParameters {
        $this->clauses[] = $clause;
        return $this;
    }

    /**
     * Adds `excludeTrashed` to the scopes array.
     * @return self
     */
    public function excludeTrashed() {
        return $this->addClause(new ExcludeTrashedScope());
    }

    /**
     * Adds `excludeVersions` to the scopes array.
     * @return self
     */
    public function excludeVersions() {
        return $this->addClause(new ExcludeVersionsScope());
    }

    /**
     * Adds `onlyTrashed` to the scopes array.
     * @return self
     */
    public function onlyTrashed() {
        return $this->addClause(new OnlyTrashedScope());
    }

    /**
     * Gets the scopes for the query
     *
     * @return array
     */
    public function getClauses() {
        return $this->clauses;
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
