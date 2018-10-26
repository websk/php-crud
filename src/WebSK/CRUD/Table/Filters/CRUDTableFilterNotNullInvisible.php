<?php

namespace WebSK\CRUD\Table\Filters;

use Slim\Http\Request;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterInvisible;

/**
 * Class CRUDTableFilterNotNullInvisible
 * @package WebSK\CRUD
 */
class CRUDTableFilterNotNullInvisible implements InterfaceCRUDTableFilterInvisible
{
    /** @var string */
    protected $field_name;

    /**
     * CRUDTableFilterNotNullInvisible constructor.
     * @param string $field_name
     */
    public function __construct(string $field_name)
    {
        $this->setFieldName($field_name);
    }

    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(Request $request): array
    {
        $column_name = $this->getFieldName();

        $where = $column_name . " is not null ";

        return [$where, []];
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
}
