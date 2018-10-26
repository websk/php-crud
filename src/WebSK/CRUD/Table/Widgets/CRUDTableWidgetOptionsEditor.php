<?php

namespace WebSK\CRUD\Table\Widgets;

use OLOG\HTML;
use OLOG\Operations;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

class CRUDTableWidgetOptionsEditor implements InterfaceCRUDTableWidget
{
    /** @var string */
    protected $field_name;
    /** @var array */
    protected $options_arr;
    /** @var string */
    protected $crudtable_id;

    /**
     * CRUDTableWidgetOptionsEditor constructor.
     * @param string $field_name
     * @param array $options_arr
     * @param string $crudtable_id
     */
    public function __construct(string $field_name, array $options_arr, string $crudtable_id)
    {
        $this->setFieldName($field_name);
        $this->setOptionsArr($options_arr);
        $this->setCrudtableId($crudtable_id);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        return HTML::tag('form', ['class' => 'js-options-editor'], function () use ($obj) {
            echo '<input type="hidden" name="' . Operations::FIELD_NAME_OPERATION_CODE . '" '.
                'value="' . CRUDTable::OPERATION_UPDATE_ENTITY_FIELD . '">';
            echo '<input type="hidden" name="' . CRUDTable::FIELD_FIELD_NAME . '" '.
                'value="' . $this->getFieldName() . '">';
            echo '<input type="hidden" name="' . CRUDTable::FIELD_CRUDTABLE_ID . '" '.
                'value="' . $this->getCrudtableId() . '">';
            echo '<input type="hidden" name="' . CRUDTable::FIELD_ENTITY_ID . '" value="' . $obj->getId() . '">';
            echo '<input type="hidden" name="' . CRUDTable::FIELD_FIELD_VALUE . '" '.
                'value="' .  CRUDFieldsAccess::getObjectFieldValue($obj, $this->getFieldName()) . '">';

            $options_arr = $this->getOptionsArr();
            $obj_value = CRUDFieldsAccess::getObjectFieldValue($obj, $this->getFieldName());
            foreach ($options_arr as $value => $option_name) {
                $disabled = '';
                if ($value == $obj_value) {
                    $disabled = 'style="opacity:0.5;" disabled';
                }
                echo '<button class="btn btn-xs btn-default" type="submit" '.
                    'name="' . CRUDTable::FIELD_FIELD_VALUE . '" '.
                    'value="' . $value . '" ' . $disabled . '>' . $option_name . '</button>';
            }
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
     * @return array
     */
    public function getOptionsArr(): array
    {
        return $this->options_arr;
    }

    /**
     * @param array $options_arr
     */
    public function setOptionsArr(array $options_arr): void
    {
        $this->options_arr = $options_arr;
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
