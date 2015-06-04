<?php

namespace Oxygen\Data\Repository;

use Oxygen\Data\Exception\NoResultException;

interface RepositoryInterface {

    /**
     * Retrieves all entities.
     *
     * @param QueryParameters  $queryParameters extra query parameters
     * @return mixed
     */
    public function all(QueryParameters $queryParameters = null);

    /**
     * Retrieves certain columns of entities.
     *
     * @param array $fields
     * @param QueryParameters  $queryParameters extra query parameters
     * @return mixed
     */
    public function columns(array $fields, QueryParameters $queryParameters = null);

    /**
     * Retrieves all entities, by page.
     *
     * @param int          $perPage
     * @param QueryParameters  $queryParameters extra query parameters
     * @return mixed
     */
    public function paginate($perPage = 25, QueryParameters $queryParameters = null);

    /**
     * Retrieves a single entity.
     *
     * @param integer       $id
     * @param QueryParameters  $queryParameters extra query parameters
     * @return object
     * @throws NoResultException if no result was found
     */
    public function find($id, QueryParameters $queryParameters = null);

    /**
     * Creates a new entity
     *
     * @return object
     */
    public function make();

    /**
     * Persists an entity.
     *
     * @param object $entity
     * @return void
     */
    public function persist($entity);

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
     * @param $key
     * @param $value
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

}
