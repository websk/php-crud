<?php

namespace WebSK\CRUD\Table\Filters;

use Slim\Http\Request;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterInvisible;

/**
 * Class CRUDTableFilterEqualInvisible
 * @package WebSK\CRUD
 */
class CRUDTableFilterEqualInvisible implements InterfaceCRUDTableFilterInvisible
{
    /** @var string */
    protected $field_name;
    /** @var string */
    protected $filter_value;

    /**
     * CRUDTableFilterEqualInvisible constructor.
     * @param $field_name
     * @param $filter_value
     */
    public function __construct($field_name, $filter_value)
    {
        $this->setFieldName($field_name);
        $this->setFilterValue($filter_value);
    }

    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(Request $request): array
    {
        $filter_value = $this->getFilterValue();
        $column_name = $this->getFieldName();
        $placeholder_values_arr = [];

        if (is_null($filter_value)) {
            $where = $column_name . ' is null ';
        } else {
            $where = $column_name . ' = ? ';
            $placeholder_values_arr[] = $filter_value;
        }

        return [$where, $placeholder_values_arr];
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->field_name;
    }

    /**
     * @param string $field_name
     */
    public function setFieldName(string $field_name): void
    {
        $this->field_name = $field_name;
    }

    /**
     * @return string
     */
    public function getFilterValue(): string
    {
        return $this->filter_value;
    }

    /**
     * @param string $filter_value
     */
    public function setFilterValue(string $filter_value): void
    {
        $this->filter_value = $filter_value;
    }
}
