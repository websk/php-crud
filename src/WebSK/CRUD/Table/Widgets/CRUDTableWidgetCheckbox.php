<?php

namespace WebSK\CRUD\Table\Widgets;

use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetCheckbox
 * @package WebSK\CRUD
 */
class CRUDTableWidgetCheckbox implements InterfaceCRUDTableWidget
{
    /** @var string */
    protected $field_name;

    /**
     * CRUDTableWidgetCheckbox constructor.
     * @param string $field_name
     */
    public function __construct(string $field_name)
    {
        $this->setFieldName($field_name);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        if (CRUDFieldsAccess::getObjectFieldValue($obj, $this->getFieldName())) {
            $html = '<span style ="text-decoration: none;" class="glyphicon glyphicon-check"></span>';
        } else {
            $html = '<span style ="text-decoration: none;" class="glyphicon glyphicon-unchecked"></span>';
        }

        return $html;
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
