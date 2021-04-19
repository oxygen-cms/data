<?php


namespace Oxygen\Data\Repository;


use DateTime;
use Doctrine\ORM\QueryBuilder;

class ExcludeTrashedScope implements QueryClauseInterface {

    public function apply(QueryBuilder $qb, string $alias): QueryBuilder {
        $time = new DateTime();

        return $qb
            ->andWhere($qb->expr()->orX("$alias.deletedAt is NULL", "$alias.deletedAt > :currentTimestamp"))
            ->setParameter('currentTimestamp', $time->format('Y-m-d H:i:s'));
    }
}
