<?php

namespace WebSK\CRUD\Table\Filters;

use Slim\Http\Request;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterInvisible;

/**
 * Class CRUDTableFilterInInvisible
 * @package WebSK\CRUD
 */
class CRUDTableFilterInInvisible implements InterfaceCRUDTableFilterInvisible
{
    protected string $field_name;

    protected array $filter_value_arr;

    /**
     * CRUDTableFilterInInvisible constructor.
     * @param string $field_name
     * @param array $filter_value_arr
     */
    public function __construct(string $field_name, array $filter_value_arr)
    {
        $this->setFieldName($field_name);
        $this->setFilterValueArr($filter_value_arr);
    }

    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(Request $request): array
    {
        $filter_value_arr = $this->getFilterValueArr();
        if (!count($filter_value_arr)) {
            return ['', []];
        }

        $placeholder_values_arr = [];
        $column_name = $this->getFieldName();

        $in_arr = [];
        foreach ($filter_value_arr as $val) {
            $in_arr[] = '?';
            $placeholder_values_arr[] = $val;
        }
        $where = $column_name . " IN(" . implode(',', $in_arr) . ") ";

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
     * @return array
     */
    public function getFilterValueArr(): array
    {
        return $this->filter_value_arr;
    }

    /**
     * @param array $filter_value_arr
     */
    public function setFilterValueArr(array $filter_value_arr): void
    {
        $this->filter_value_arr = $filter_value_arr;
    }
}
