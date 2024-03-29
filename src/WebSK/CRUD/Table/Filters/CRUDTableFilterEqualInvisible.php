<?php

namespace WebSK\CRUD\Table\Filters;

use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterInvisible;

/**
 * Class CRUDTableFilterEqualInvisible
 * @package WebSK\CRUD
 */
class CRUDTableFilterEqualInvisible implements InterfaceCRUDTableFilterInvisible
{
    protected string $field_name;

    protected ?string $filter_value = null;

    /**
     * CRUDTableFilterEqualInvisible constructor.
     * @param string $field_name
     * @param string|null $filter_value
     */
    public function __construct(string $field_name, ?string $filter_value)
    {
        $this->setFieldName($field_name);
        $this->setFilterValue($filter_value);
    }

    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(ServerRequestInterface $request): array
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
     * @return string|null
     */
    public function getFilterValue(): ?string
    {
        return $this->filter_value;
    }

    /**
     * @param string|null $filter_value
     */
    public function setFilterValue(?string $filter_value): void
    {
        $this->filter_value = $filter_value;
    }
}
