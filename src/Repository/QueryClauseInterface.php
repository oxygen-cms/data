<?php


namespace Oxygen\Data\Repository;

use Doctrine\ORM\QueryBuilder;

interface QueryClauseInterface {

    public function apply(QueryBuilder $qb, string $alias): QueryBuilder;

}
