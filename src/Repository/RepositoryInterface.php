<?php

namespace Oxygen\Data\Repository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Oxygen\Data\Exception\NoResultException;

interface RepositoryInterface {

    /**
     * Retrieves all entities.
     *
     * @param QueryParameters|null $queryParameters extra query parameters
     * @return mixed
     */
    public function all(QueryParameters $queryParameters = null);

    /**
     * Retrieves certain columns of entities.
     *
     * @param array $fields
     * @param QueryParameters|null $queryParameters extra query parameters
     * @return mixed
     */
    public function columns(array $fields, QueryParameters $queryParameters = null);

    /**
     * Retrieves all entities, by page.
     *
     * @param int $perPage items per page
     * @param QueryParameters|null $queryParameters an optional array of query scopes
     * @param int|null $currentPage current page that overrides the pagination service
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 25, ?QueryParameters $queryParameters = null, ?int $currentPage = null): LengthAwarePaginator;

    /**
     * Retrieves a single entity.
     *
     * @param integer $id
     * @param QueryParameters|null $queryParameters extra query parameters
     * @return object
     * @throws NoResultException if no result was found
     */
    public function find(int $id, QueryParameters $queryParameters = null);

    /**
     * Creates a new entity
     *
     * @return object
     */
    public function make();

    /**
     * Persists an entity.
     *
     * @param object  $entity
     * @param boolean $flush
     * @return void
     */
    public function persist($entity, $flush = true);

    /**
     * Flushes changes to the database.
     *
     * @return void
     */
    public function flush();

    /**
     * Deletes an entity.
     *
     * @param object $entity
     * @return void
     */
    public function delete($entity);

    /**
     * Lists columns of the entity like this:
     *  1 => Title
     *  3 => Foo
     *  4 => Yoyo
     *
     * @param string $key
     * @param string $value
     * @param QueryParameters  $queryParameters extra query parameters
     * @return array
     */
    public function listKeysAndValues($key, $value, QueryParameters $queryParameters = null);

    /**
     * Returns a reference to a specified item.
     *
     * @param int $id
     * @return object
     */
    public function getReference($id);

    /**
     * Returns the class name of the entity.
     *
     * @return string
     */
    public function getEntityName(): string;

}
