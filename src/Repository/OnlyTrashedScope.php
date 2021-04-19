<?php


namespace Oxygen\Data\Repository;


use DateTime;
use Doctrine\ORM\QueryBuilder;

class OnlyTrashedScope implements QueryClauseInterface {

    public function apply(QueryBuilder $qb, string $alias): QueryBuilder {
        $time = new DateTime();

        return $qb
            ->andWhere("$alias.deletedAt is not NULL")
            ->andWhere(":currentTimestamp > $alias.deletedAt")
            ->setParameter('currentTimestamp', $time->format('Y-m-d H:i:s'));
    }
}
