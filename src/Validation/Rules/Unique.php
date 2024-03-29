<?php

namespace Oxygen\Data\Validation\Rules;

use Oxygen\Data\Validation\ValidationService;

class Unique {

    /**
     * @var string
     */
    private $uniqueFieldName;
    /**
     * @var string
     */
    private $entityName;
    /**
     * @var int|null
     */
    private $id;
    /**
     * @var string|null
     */
    private $idField;
    /**
     * @var array
     */
    private array $wheres;

    public function __construct(string $entityName) {
        $this->entityName = $entityName;
        $this->wheres = [];
        $this->id = null;
        $this->idField = null;
    }

    /**
     * @param string $fieldName
     * @return $this
     */
    public function field(string $fieldName): Unique {
        $this->uniqueFieldName = $fieldName;
        return $this;
    }

    /**
     * @param int|null $id
     * @param string $idField
     * @return $this
     */
    public function ignoreWithId(?int $id, string $idField = 'id'): Unique {
        if(is_null($id)) {
            $id = ValidationService::NULL;
        }
        $this->id = $id;
        $this->idField = $idField;
        return $this;
    }

    /**
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @return $this
     */
    public function addWhere(string $field, string $operator, $value): Unique {
        if($value === null) {
            $value = ValidationService::NULL;
        }
        $this->wheres[] = ['field' => $field, 'operator' => $operator, 'value' => $value];
        return $this;
    }

    public static function amongst(string $entityName): Unique {
        return new Unique($entityName);
    }

    public function __toString() {
        $base = "unique:$this->entityName,$this->uniqueFieldName";
        if($this->id != null) {
            $base .= ",$this->id,$this->idField";
        }
        foreach($this->wheres as $where) {
            $field = $where['field'];
            $operator = $where['operator'];
            $value = $where['value'];
            $base .= ",$field,$operator,$value";
        }
        return $base;
    }

}
