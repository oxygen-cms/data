<?php


namespace Oxygen\Data\Repository;


use Doctrine\ORM\QueryBuilder;

class ExcludeVersionsScope implements QueryClauseInterface {

    public function apply(QueryBuilder $qb, string $alias): QueryBuilder {
        return $qb->andWhere($alias . '.headVersion is NULL');
    }
}
