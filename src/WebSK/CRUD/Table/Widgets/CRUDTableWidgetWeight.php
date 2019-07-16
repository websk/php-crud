<?php

namespace WebSK\CRUD\Table\Widgets;

use WebSK\CRUD\CRUDCompiler;
use WebSK\Utils\Assert;
use WebSK\CRUD\CRUD;
use OLOG\Operations;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Table\NullablePostFields;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetWeight
 * @package WebSK\CRUD
 */
class CRUDTableWidgetWeight implements InterfaceCRUDTableWidget
{
    const FORMFIELD_CONTEXT_FIELDS_NAME = 'context_field_names';

    /** @var array */
    protected $context_fields_arr = [];

    /** @var string */
    protected $button_text;

    /** @var string */
    protected $button_class_str;

    /**
     * CRUDTableWidgetWeight constructor.
     * @param array $context_fields_arr
     * @param string $button_text
     * @param string $button_class_str
     */
    public function __construct(
        array $context_fields_arr,
        string $button_text = '',
        string $button_class_str = 'btn btn-xs btn-default glyphicon glyphicon-arrow-up'
    ) {
        $this->setContextFieldsArr($context_fields_arr);
        $this->setButtonText($button_text);
        $this->setButtonClassStr($button_class_str);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        Assert::assert($obj);
        //@TODO check $obj implements InterfaceWeight

        $o = '';
        $o .= '<form style="display: inline;" method="post">';
        $o .= Operations::operationCodeHiddenField(CRUDTable::OPERATION_SWAP_ENTITY_WEIGHT);
        $o .= '<input type="hidden" name="' . self::FORMFIELD_CONTEXT_FIELDS_NAME . '" value="' .
            Sanitize::sanitizeAttrValue(implode(',', array_keys($this->getContextFieldsArr()))) . '">';

        foreach ($this->getContextFieldsArr() as $context_field_name => $context_field_value) {
            $context_field_value = CRUDCompiler::fieldValueOrCallableResult($context_field_value, $obj);
            $o .= NullablePostFields::hiddenFieldHtml($context_field_name, $context_field_value);
        }

        $o .= '<input type="hidden" name="_class_name" value="' . Sanitize::sanitizeAttrValue(get_class($obj)) . '">';
        $o .= '<input type="hidden" name="_id" value="' .
            Sanitize::sanitizeAttrValue(CRUDFieldsAccess::getObjId($obj)) . '">';

        $o .= '<button class="' . $this->getButtonClassStr() . '" type="submit">' . $this->getButtonText() . '</button>';

        $o .= '</form>';

        return $o;
    }

    /**
     * @return array
     */
    public function getContextFieldsArr(): array
    {
        return $this->context_fields_arr;
    }

    /**
     * @param array $context_fields_arr
     */
    public function setContextFieldsArr(array $context_fields_arr): void
    {
        $this->context_fields_arr = $context_fields_arr;
    }

    /**
     * @return string
     */
    public function getButtonText(): string
    {
        return $this->button_text;
    }

    /**
     * @param string $button_text
     */
    public function setButtonText(string $button_text): void
    {
        $this->button_text = $button_text;
    }

    /**
     * @return string
     */
    public function getButtonClassStr(): string
    {
        return $this->button_class_str;
    }

    /**
     * @param string $button_class_str
     */
    public function setButtonClassStr(string $button_class_str): void
    {
        $this->button_class_str = $button_class_str;
    }
}
