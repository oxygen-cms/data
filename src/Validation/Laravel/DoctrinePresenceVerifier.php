<?php

namespace Oxygen\Data\Validation\Laravel;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Illuminate\Validation\PresenceVerifierInterface;
use InvalidArgumentException;

class DoctrinePresenceVerifier implements PresenceVerifierInterface {

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
    public function __construct(ManagerRegistry $registry)
    {
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
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = []) {
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
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     * @param  object $qb
     * @param  string $key
     * @param  string $extraValue
     * @param  string  $alias
     * @return void
     */
    protected function addWhere($qb, $key, $extraValue, $alias = 'o') {
        $reference = $key . '.' . $alias;
        if($extraValue === 'NULL') {
            $qb->andWhere($reference . ' is NULL');
        } elseif($extraValue === 'NOT_NULL') {
            $qb->andWhere($reference . ' is NOT NULL');
        } elseif(is_array($extraValue)) {
            list($operator, $value) = $extraValue;
            $this->addWhereAdvanced($qb, $key, $operator, $value, $alias);
        } else {
            $this->addWhereAdvanced($qb, $key, '=', $extraValue, $alias);
        }
    }

    /**
     * Add a "where" clause to the given query.
     *
     * @param  object $qb
     * @param  string $key
     * @param  string $operator
     * @param  mixed  $value
     * @return void
     */
    protected function addWhereAdvanced($qb, $key, $operator, $value, $alias) {
        $parameter = 'where' . ucfirst($key);
        $qb->andWhere($alias . '.' . $key .  ' ' . $operator . ' :'  . $parameter)
           ->setParameter($parameter, $value);
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
     *
     * @return \Doctrine\Persistence\ObjectManager|null
     */
    protected function getEntityManager($entity)
    {
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
     *
     * @return void
     */
    public function setConnection($connection) {
        $this->connection = $connection;
    }

}
