<?php

namespace Oxygen\Data\Repository\Doctrine;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use InvalidArgumentException;
use Doctrine\ORM\NoResultException as DoctrineNoResultException;
use Oxygen\Data\Exception\NoResultException;
use Oxygen\Data\Repository\QueryParameters;
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

    protected $entityName = 'Undefined';

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
     * @param QueryParameters  $queryParameters extra query parameters
     * @return mixed
     */
    public function all(QueryParameters $queryParameters = null) {
        return $this->getQuery(
            $this->createSelectQuery(),
            $queryParameters
        )->getResult();
    }

    /**
     * Retrieves certain columns of entities.
     *
     * @param array           $fields
     * @param QueryParameters $queryParameters an optional array of query scopes
     * @return mixed
     */
    public function columns(array $fields, QueryParameters $queryParameters = null) {
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

        return $this->getQuery($qb, $queryParameters)->getResult();
    }

    /**
     * Lists columns of the entity like this:
     *  1 => Title
     *  3 => Foo
     *  4 => Yoyo
     *
     * @param $key
     * @param $value
     * @param QueryParameters $queryParameters
     * @return array
     */
    public function listKeysAndValues($key, $value, QueryParameters $queryParameters = null) {
        $results = $this->columns([$key, $value], $queryParameters);

        $return = [];
        foreach($results as $result) {
            $return[$result[$key]] = $result[$value];
        }

        return $return;
    }

    /**
     * Retrieves all entities, by page.
     *
     * @param int             $perPage          items per page
     * @param QueryParameters $queryParameters  an optional array of query scopes
     * @param int             $currentPage      current page that overrides the pagination service
     * @return mixed
     */
    public function paginate($perPage = 25, QueryParameters $queryParameters = null, $currentPage = null) {
        $currentPage = $currentPage === null ? $this->paginator->getCurrentPage() : $currentPage;
        $items = $this->getQuery(
            $this->createSelectQuery()
                ->setFirstResult($perPage * ($currentPage - 1))
                ->setMaxResults($perPage),
            $queryParameters
        )->getResult();

        return $this->paginator->make($items, $this->count($queryParameters), $perPage);
    }

    /**
     * Retrieves a single entity.
     *
     * @param integer         $id
     * @param QueryParameters $queryParameters an optional array of query scopes
     * @return object
     * @throws NoResultException if no result was found
     */
    public function find($id, QueryParameters $queryParameters = null) {
        $q = $this->getQuery(
            $this->createSelectQuery()
                 ->andWhere('o.id = :id')
                 ->setParameter('id', $id),
            $queryParameters
        );

        try {
            return $q->getSingleResult();
        } catch(DoctrineNoResultException $e) {
            throw $this->makeNoResultException($e, $q);
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
     * @param QueryParameters $queryParameters
     * @return integer
     */
    public function count(QueryParameters $queryParameters = null) {
        return (int) $this->getQuery(
            $this->entities->createQueryBuilder()
                ->select('count(o.id)')
                ->from($this->entityName, 'o'),
            $queryParameters
        )->getSingleScalarResult();
    }

    /**
     * Creates a new QueryBuilder instance that is pre-populated for this entity name.
     *
     * @param string $alias
     * @param string $indexBy The index for the from.
     *
     * @return QueryBuilder
     */
    protected function createSelectQuery($alias = 'o', $indexBy = null) {
        $qb = $this->entities->createQueryBuilder()
                             ->select($alias)
                             ->from($this->entityName, $alias, $indexBy);
        return $qb;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder              $qb
     * @param \Oxygen\Data\Repository\QueryParameters $queryParameters
     * @return \Doctrine\ORM\Query
     */
    protected function getQuery(QueryBuilder $qb, QueryParameters $queryParameters = null, $alias = 'o') {
        if($queryParameters != null) {
            $this->applyScopesToQueryBuilder($qb, $queryParameters->getScopes());
            if($queryParameters->getOrderBy() != null) {
                $this->applyOrderByToQueryBuilder($qb, $queryParameters->getOrderBy(), $alias);
            }
        }

        return $qb->getQuery();
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
    protected function applyScopesToQueryBuilder(QueryBuilder $qb, array $scopes) {
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
     * Applies the order by request to the query builder.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array                      $orderBy
     * @param string                     $alias
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function applyOrderByToQueryBuilder(QueryBuilder $queryBuilder, array $orderBy, $alias) {
        $queryBuilder->orderBy($alias . '.' . $orderBy[0], $orderBy[1]);
        return $queryBuilder;
    }

    /**
     * Creates a NoResultException from a QueryBuilder
     *
     * @param \Exception                    $e
     * @param \Doctrine\ORM\QueryBuilder    $qb
     * @return \Oxygen\Data\Exception\NoResultException
     */
    protected function makeNoResultException(\Exception $e, Query $q) {
        return new NoResultException($e, $this->replaceQueryParameters($q->getDQL(), $q->getParameters()));
    }
    /**
     * Replaces placeholders within a given query with the actual values.
     * Used for debugging.
     *
     * @param $query
     * @param $parameters
     * @return string
     */
    private function replaceQueryParameters($query, $parameters) {
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
