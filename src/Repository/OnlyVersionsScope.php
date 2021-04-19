<?php


namespace Oxygen\Data\Repository;


use Doctrine\ORM\QueryBuilder;

class OnlyVersionsScope implements QueryClauseInterface {

    public function apply(QueryBuilder $qb, string $alias): QueryBuilder {
        return $qb->andWhere("$alias.headVersion is NOT NULL");
    }
}
