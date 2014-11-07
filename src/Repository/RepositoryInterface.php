<?php

namespace Oxygen\Data\Repository;

use Oxygen\Data\Exception\NoResultException;

interface RepositoryInterface {

    /**
     * Retrieves all entities.
     *
     * @param array|string  $scopes an optional array of query scopes
     * @return mixed
     */

    public function all($scopes = []);

    /**
     * Retrieves certain columns of entities.
     *
     * @param array $fields
     * @param array|string  $scopes an optional array of query scopes
     * @return mixed
     */

    public function columns(array $fields, $scopes = []);

    /**
     * Retrieves all entities, by page.
     *
     * @param int          $perPage
     * @param array|string $scopes an optional array of query scopes
     * @return mixed
     */

    public function paginate($perPage = 25, $scopes = []);

    /**
     * Retrieves a single entity.
     *
     * @param integer       $id
     * @param array|string  $scopes an optional array of query scopes
     * @return object
     * @throws NoResultException if no result was found
     */

    public function find($id, $scopes = []);

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

}
