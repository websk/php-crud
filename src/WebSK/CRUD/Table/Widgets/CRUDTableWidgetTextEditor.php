<?php

namespace WebSK\CRUD\Table\Widgets;

use OLOG\HTML;
use OLOG\Operations;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetTextEditor
 * @package WebSK\CRUD\Table\Widgets
 */
class CRUDTableWidgetTextEditor implements InterfaceCRUDTableWidget
{
    /** @var string */
    protected $field_name;

    /** @var string */
    protected $text;

    /** @var string */
    protected $crudtable_id;

    /**
     * CRUDTableWidgetTextEditor constructor.
     * @param string $field_name
     * @param string $text
     * @param string $crudtable_id
     */
    public function __construct(string $field_name, string $text, string $crudtable_id)
    {
        $this->setFieldName($field_name);
        $this->setText($text);
        $this->setCrudtableId($crudtable_id);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        return HTML::tag('form', ['class' => 'js-text-editor'], function () use ($obj) {
            echo '<input type="hidden" name="' . Operations::FIELD_NAME_OPERATION_CODE .
                '" value="' . CRUDTable::OPERATION_UPDATE_ENTITY_FIELD . '">';
            echo '<input type="hidden" name="' . CRUDTable::FIELD_FIELD_NAME .
                '" value="' . $this->getFieldName() . '">';
            echo '<input type="hidden" name="' . CRUDTable::FIELD_CRUDTABLE_ID .
                '" value="' . $this->getCrudtableId() . '">';
            echo '<input type="hidden" name="' . CRUDTable::FIELD_ENTITY_ID .
                '" value="' . $obj->getId() . '">';
            echo '<input type="text" name="' . CRUDTable::FIELD_FIELD_VALUE .
                '" value="' .  CRUDFieldsAccess::getObjectFieldValue($obj, $this->getFieldName()) . '">';
            echo '<button class="btn btn-xs btn-default glyphicon glyphicon-ok" type="submit"></button>';
        });
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
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getCrudtableId(): string
    {
        return $this->crudtable_id;
    }

    /**
     * @param string $crudtable_id
     */
    public function setCrudtableId(string $crudtable_id): void
    {
        $this->crudtable_id = $crudtable_id;
    }
}
