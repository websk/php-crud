<?php

namespace WebSK\CRUD\Table\Filters;

use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterInvisible;

/**
 * Class CRUDTableFilterNotNullInvisible
 * @package WebSK\CRUD
 */
class CRUDTableFilterNotNullInvisible implements InterfaceCRUDTableFilterInvisible
{
    protected string $field_name;

    /**
     * CRUDTableFilterNotNullInvisible constructor.
     * @param string $field_name
     */
    public function __construct(string $field_name)
    {
        $this->setFieldName($field_name);
    }

    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(ServerRequestInterface $request): array
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
