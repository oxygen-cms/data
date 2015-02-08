<?php

namespace Oxygen\Data\Repository\Doctrine;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use Doctrine\ORM\NoResultException as DoctrineNoResultException;
use Oxygen\Data\Exception\NoResultException;
use Oxygen\Data\Repository\RepositoryInterface;
use Oxygen\Data\Pagination\PaginationService;

class Repository implements RepositoryInterface {

    /**
     * The entity manager
     *
     * @var EntityManagerInterface
     */

    protected $entities;

    /**
     * The pagination service
     *
     * @var PaginationService
     */

    protected $paginator;

    /**
     * The name of the entity.
     *
     * @var string
     */

    protected $entityName;

    /**
     * Constructs the DoctrineRepository.
     *
     * @param EntityManagerInterface   $entities
     * @param PaginationService        $paginator
     */

    public function __construct(EntityManagerInterface $entities, PaginationService $paginator) {
        $this->entities = $entities;
        $this->paginator = $paginator;
    }

    /**
     * Retrieves all entities.
     *
     * @param array|string   $scopes an optional array of query scopes
     * @return mixed
     */

    public function all($scopes = []) {
        return $this->createScopedQueryBuilder($scopes)->getQuery()->getResult();
    }

    /**
     * Retrieves certain columns of entities.
     *
     * @param array        $fields
     * @param array|string $scopes an optional array of query scopes
     * @return mixed
     */
    public function columns(array $fields, $scopes = []) {
        $select = '';
        foreach($fields as $field) {
            $select .= 'o.' . $field;
            if($field !== last($fields)) {
                $select .= ', ';
            }
        }
        $qb = $this->entities->createQueryBuilder()
            ->select($select)
            ->from($this->entityName, 'o');
        $qb = $this->createScopedQueryBuilder($scopes, $qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * Lists columns of the entity like this:
     *  1 => Title
     *  3 => Foo
     *  4 => Yoyo
     *
     * @param $key
     * @param $value
     * @param array $scopes
     * @return array
     */

    public function listKeysAndValues($key, $value, $scopes = []) {
        $results = $this->columns([$key, $value], $scopes);

        $return = [];
        foreach($results as $result) {
            $return[$result[$key]] = $result[$value];
        }

        return $return;
    }

    /**
     * Retrieves all entities, by page.
     *
     * @param int          $perPage     items per page
     * @param array|string $scopes      an optional array of query scopes
     * @param int          $currentPage current page that overrides the pagination service
     * @return mixed
     */
    public function paginate($perPage = 25, $scopes = [], $currentPage = null) {
        $currentPage = $currentPage === null ? $this->paginator->getCurrentPage() : $currentPage;
        $items = $this->createScopedQueryBuilder($scopes)
            ->setFirstResult($perPage * ($currentPage - 1))
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        return $this->paginator->make($items, $this->count($scopes), $perPage);
    }

    /**
     * Retrieves a single entity.
     *
     * @param integer       $id
     * @param array|string  $scopes an optional array of query scopes
     * @return object
     * @throws NoResultException if no result was found
     */

    public function find($id, $scopes = []) {
        try {
            return $this->createScopedQueryBuilder($scopes)
                ->andWhere('o.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleResult();
        } catch(DoctrineNoResultException $e) {
            throw new NoResultException($e);
        }
    }

    /**
     * Creates a new entity
     *
     * @return object
     */

    public function make() {
        return new $this->entityName();
    }

    /**
     * Persists an entity.
     *
     * @param object  $entity
     * @param boolean $flush
     * @return void
     */

    public function persist($entity, $flush = true) {
        $this->entities->persist($entity);
        if($flush) {
            $this->entities->flush();
        }
    }

    /**
     * Flushes changes.
     *
     * @return void
     */

    public function flush() {
        $this->entities->flush();
    }

    /**
     * Deletes an entity.
     *
     * @param  object  $entity
     * @param  boolean $flush
     * @return void
     */

    public function delete($entity, $flush = true) {
        $this->entities->remove($entity);
        if($flush) {
            $this->entities->flush();
        }
    }

    /**
     * Retrieves the number of records in the table.
     *
     * @param array $scopes
     * @return integer
     */

    public function count($scopes = []) {
        $qb = $this->entities->createQueryBuilder()
            ->select('count(o.id)')
            ->from($this->entityName, 'o');

        return (int) $this->createScopedQueryBuilder($scopes, $qb)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Creates a new QueryBuilder instance that is pre-populated for this entity name.
     * Applies scopes to the query builder as well.
     *
     * @param array  $scopes
     * @param QueryBuilder $qb
     * @throws InvalidArgumentException if the scope was not found
     * @return QueryBuilder
     */

    protected function createScopedQueryBuilder($scopes, $qb = null) {
        if($qb === null) {
            $qb = $this->createQueryBuilder();
        }
        foreach((array) $scopes as $scope) {
            $method = 'scope' . ucfirst($scope);
            if(method_exists($this, $method)) {
                $qb = $this->{$method}($qb);
            } else {
                throw new InvalidArgumentException('Scope \'' . $scope . '\' not found');
            }
        }
        return $qb;
    }

    /**
     * Creates a new QueryBuilder instance that is pre-populated for this entity name.
     *
     * @param string $alias
     * @param string $indexBy The index for the from.
     * @return QueryBuilder
     */

    protected function createQueryBuilder($alias = 'o', $indexBy = null) {
        return $this->entities->createQueryBuilder()
                    ->select($alias)
                    ->from($this->entityName, $alias, $indexBy);
    }

    /**
     * Replaces placeholders within a given query with the actual values.
     * Used for debugging.
     *
     * @param $query
     * @param $parameters
     * @return string
     */

    protected function replaceQueryParameters($query, $parameters) {
        foreach($parameters as $parameter) {
            $value = $parameter->getValue();
            $value = $value instanceof DateTime ? $value->format('Y-m-d H:i:s') : $value;
            $query = str_replace(':' . $parameter->getName(), $value, $query);
        }
        return $query;
    }

    /**
     * Returns a reference to a specified item.
     *
     * @param int $id
     * @return object
     */

    public function getReference($id) {
        return $this->entities->getReference($this->entityName, $id);
    }

}
