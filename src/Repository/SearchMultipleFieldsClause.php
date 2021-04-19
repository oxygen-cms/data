<?php


namespace Oxygen\Data\Repository;


use Doctrine\ORM\QueryBuilder;

class SearchMultipleFieldsClause implements QueryClauseInterface {

    /**
     * @var array
     */
    private $fields;
    /**
     * @var string
     */
    private $searchQuery;

    public function __construct(array $fields, string $query) {
        $this->fields = $fields;
        $this->searchQuery = $query;
    }

    public function apply(QueryBuilder $qb, string $alias): QueryBuilder {
        $likes = [];
        foreach($this->fields as $field) {
            $likes[] = $qb->expr()->like($alias . '.' . $field, ':searchQuery');
        }

        return $qb
            ->andWhere(call_user_func_array([$qb->expr(), 'orX'], $likes))
            ->setParameter('searchQuery', '%' . $this->searchQuery . '%');
    }
}
