<?php

namespace WebSK\CRUD\Table\Widgets;

use WebSK\CRUD\CRUDCompiler;
use WebSK\CRUD\CRUDOperations;
use WebSK\Entity\InterfaceEntity;
use WebSK\Entity\InterfaceWeight;
use WebSK\Utils\Assert;
use WebSK\CRUD\CRUD;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Table\NullablePostFields;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetWeight
 * @package WebSK\CRUD
 */
class CRUDTableWidgetWeight implements InterfaceCRUDTableWidget
{
    const string FORMFIELD_CONTEXT_FIELDS_NAME = 'context_field_names';

    protected array $context_fields_arr = [];

    protected string $button_text = '';

    protected string $button_class_str = '';

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
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string
    {
        Assert::assert($entity_obj);

        $entity_class_name = get_class($entity_obj);

        $interfaces_arr = class_implements($entity_class_name);
        Assert::assert(
            $interfaces_arr && in_array(InterfaceWeight::class, $interfaces_arr),
            'Class ' . $entity_class_name . ' does not implement interface ' . InterfaceWeight::class
        );

        $content_html = '';
        $content_html .= '<form style="display: inline;" method="post">';
        $content_html .= '<input type="hidden" name="' . CRUDOperations::FIELD_NAME_OPERATION_CODE . '" value="' . Sanitize::sanitizeAttrValue(CRUDOperations::OPERATION_SWAP_ENTITY_WEIGHT) . '">';
        $content_html .= '<input type="hidden" name="' . self::FORMFIELD_CONTEXT_FIELDS_NAME . '" value="' .
            Sanitize::sanitizeAttrValue(implode(',', array_keys($this->getContextFieldsArr()))) . '">';

        foreach ($this->getContextFieldsArr() as $context_field_name => $context_field_value) {
            $context_field_value = CRUDCompiler::fieldValueOrCallableResult($context_field_value, $entity_obj);
            $content_html .= NullablePostFields::hiddenFieldHtml($context_field_name, $context_field_value);
        }

        $content_html .= '<input type="hidden" name="_class_name" value="' . Sanitize::sanitizeAttrValue(get_class($entity_obj)) . '">';
        $content_html .= '<input type="hidden" name="_id" value="' .
            Sanitize::sanitizeAttrValue(CRUDFieldsAccess::getObjId($entity_obj)) . '">';

        $content_html .= '<button class="' . $this->getButtonClassStr() . '" type="submit">' . $this->getButtonText() . '</button>';

        $content_html .= '</form>';

        return $content_html;
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
