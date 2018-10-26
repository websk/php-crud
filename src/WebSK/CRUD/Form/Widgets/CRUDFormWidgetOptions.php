<?php

namespace WebSK\CRUD\Form\Widgets;

use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;

/**
 * Class CRUDFormWidgetOptions
 * @package WebSK\CRUD
 */
class CRUDFormWidgetOptions implements InterfaceCRUDFormWidget
{
    /** @var string */
    protected $field_name;
    /** @var array */
    protected $options_arr;
    /** @var bool */
    protected $show_null_checkbox;
    /** @var bool */
    protected $is_required;

    /**
     * CRUDFormWidgetOptions constructor.
     * @param string $field_name
     * @param array $options_arr
     * @param bool $show_null_checkbox
     * @param bool $is_required
     */
    public function __construct(
        string $field_name,
        array $options_arr,
        bool $show_null_checkbox = false,
        bool $is_required = false
    ) {
        $this->setFieldName($field_name);
        $this->setOptionsArr($options_arr);
        $this->setShowNullCheckbox($show_null_checkbox);
        $this->setIsRequired($is_required);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        return $this->htmlForValue($field_value);
    }

    /**
     * @param string $field_value
     * @param string|null $input_name
     * @return string
     */
    public function htmlForValue(string $field_value = null, string $input_name = null): string
    {
        $field_name = $this->getFieldName();
        $html = '';
        $options = '';

        if (is_null($input_name)) {
            $input_name = $field_name;
        }

        $options_arr = $this->getOptionsArr();

        foreach ($options_arr as $value => $title) {
            $selected_html_attr = '';
            if ($field_value == $value) {
                $selected_html_attr = 'selected';
            }

            $options .= '<option value="' . $value . '" ' . $selected_html_attr . '>' . $title . '</option>';
        }

        $is_null_checked = '';
        if (is_null($field_value)) {
            $is_null_checked = ' checked ';
        }


        $is_required_str = '';
        if ($this->is_required) {
            $is_required_str = ' required ';
        }

        $html .= '<div class="input-group">';
        $html .= '<select name="' . $input_name . '" class="form-control" ' . $is_required_str . '>' .
            $options . '</select>';

        if ($this->isShowNullCheckbox()) {
            $html .= '<div class="input-group-addon">';
            $html .= '<input type = "checkbox" value="1" name="' . Sanitize::sanitizeAttrValue($input_name) .
                '___is_null" ' . $is_null_checked . ' /> null';
            $html .= '</div>';
        }

        $html .= '</div>';

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
     * @return bool
     */
    public function isShowNullCheckbox(): bool
    {
        return $this->show_null_checkbox;
    }

    /**
     * @param bool $show_null_checkbox
     */
    public function setShowNullCheckbox(bool $show_null_checkbox): void
    {
        $this->show_null_checkbox = $show_null_checkbox;
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
}
