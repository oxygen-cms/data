<?php

namespace Oxygen\Data\Validation\Laravel;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Illuminate\Validation\PresenceVerifierInterface;
use InvalidArgumentException;
use Oxygen\Data\Validation\ValidationService;

class DoctrinePresenceVerifier implements PresenceVerifierInterface {

    public const OPERATORS = [
        ValidationService::EQUALS, '<', '>', '<=', '>=', ValidationService::NOT_EQUALS
    ];

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * The database connection to use.
     *
     * @var string
     */
    protected $connection = null;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry) {
        $this->registry = $registry;
    }

    /**
     * Count the number of objects in a collection having the given value.
     *
     * @param string $collection
     * @param string $column
     * @param string $value
     * @param int $excludeId
     * @param string $idColumn
     * @param array $extra
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = []): int {
        $idColumn = $idColumn !== null ? $idColumn : 'id';

        $qb = $this->createCountQuery($collection)
                   ->where('o.' . $column . ' = :value')
                   ->setParameter('value', $value);

        if (!is_null($excludeId) && $excludeId != 'NULL')  {
            $qb->andWhere('o.' . $idColumn .' <> :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        foreach($extra as $key => $extraValue) {
            $this->addWhere($qb, $key, $extraValue);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Count the number of objects in a collection with the given values.
     *
     * @param string $collection
     * @param string $column
     * @param array $values
     * @param array $extra
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getMultiCount($collection, $column, array $values, array $extra = []) {
        $qb = $this->createCountQuery($collection)
                   ->where('o.', $column . ' IN (:values)')
                   ->setParameter('values', array_values($values));

        foreach($extra as $key => $extraValue) {
            $this->addWhere($qb, $key, $extraValue);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Add a "where" clause to the given query.
     * Includes options for custom operators, e.g.: !=, >, < which Laravel Doctrine's built-in presence
     * verifier does not have.
     *
     * @param  QueryBuilder $qb
     * @param  string $key
     * @param  string|array $extraValue
     * @param  string  $alias
     * @return void
     */
    protected function addWhere(QueryBuilder $qb, string $key, $extraValue, $alias = 'o') {
        list($operator, $value) = $extraValue;
        $this->addWhereAdvanced($qb, $key, $operator, $value, $alias);
    }

    /**
     * Add a "where" clause to the given query.
     *
     * @param QueryBuilder $qb
     * @param string $key
     * @param string $operator
     * @param mixed $value
     * @param string $alias
     * @return void
     */
    protected function addWhereAdvanced(QueryBuilder $qb, string $key, string $operator, $value, $alias) {
        if($value === ValidationService::NULL) {
            $qb->andWhere($alias . '.' . $key . ' is NULL');
        } else if($value === ValidationService::NOT_NULL) {
            $qb->andWhere($alias . '.' . $key . ' is NOT NULL');
        } else {
            $parameter = 'where' . ucfirst($key);
            $qb->andWhere($alias . '.' . $key .  ' ' . $operator . ' :'  . $parameter)
                ->setParameter($parameter, $value);
        }
    }

    /**
     * Creates a query builder.
     *
     * @param string $collection
     * @param string $alias
     * @return QueryBuilder
     */
    protected function createCountQuery($collection, $alias = 'o') {
        return $this->getEntityManager($collection)->createQueryBuilder()
            ->select('COUNT(' . $alias . ')')
            ->from($collection, $alias);
    }

    /**
     * @param string $entity
     * @return EntityManagerInterface
     */
    protected function getEntityManager($entity) {
        if (!is_null($this->connection)) {
            return $this->registry->getManager($this->connection);
        }

        if (substr($entity, 0, 1) === '\\') {
            $entity = substr($entity, 1);
        }

        $em = $this->registry->getManagerForClass($entity);

        if ($em === null) {
            throw new InvalidArgumentException(sprintf("No Entity Manager could be found for [%s].", $entity));
        }

        return $em;
    }

    /**
     * Set the connection to be used.
     *
     * @param string $connection
     * @return void
     */
    public function setConnection($connection) {
        $this->connection = $connection;
    }

}
