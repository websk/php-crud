<?php

namespace WebSK\CRUD\Form\Widgets;

use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;

/**
 * Class CRUDFormWidgetTextarea
 * @package WebSK\CRUD
 */
class CRUDFormWidgetTextarea implements InterfaceCRUDFormWidget
{
    protected string $field_name;

    protected bool $is_required = false;

    protected bool $disabled = false;

    /**
     * CRUDFormWidgetTextarea constructor.
     * @param string $field_name
     * @param bool $is_required
     * @param bool $disabled
     */
    public function __construct(string $field_name, bool $is_required = false, bool $disabled = false)
    {
        $this->setFieldName($field_name);
        $this->setIsRequired($is_required);
        $this->setDisabled($disabled);
    }

    /** @inheritdoc */
    public function html($entity_obj, CRUD $crud): string
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($entity_obj, $field_name);
        $is_required_str = '';
        if ($this->isRequired()) {
            $is_required_str = ' required ';
        }

        $disabled = '';
        if ($this->isDisabled()) {
            $disabled = 'disabled';
        }

        return '<textarea name="' . Sanitize::sanitizeAttrValue($field_name) . '"  '. $is_required_str .
            ' class="form-control" rows="5"  ' . $disabled . '>' . Sanitize::sanitizeTagContent($field_value) .
            '</textarea>';
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
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->is_required;
    }

    /**
     * @param bool $is_required
     */
    public function setIsRequired(bool $is_required): void
    {
        $this->is_required = $is_required;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @param bool $disabled
     */
    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }
}
