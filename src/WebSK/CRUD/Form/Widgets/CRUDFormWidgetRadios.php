<?php

namespace WebSK\CRUD\Form\Widgets;

use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;

/**
 * Class CRUDFormWidgetRadios
 * @package WebSK\CRUD
 */
class CRUDFormWidgetRadios implements InterfaceCRUDFormWidget
{
    /** @var string */
    protected $field_name;
    /** @var array */
    protected $options_arr;
    /** @var bool */
    protected $show_null_checkbox;
    /** @var bool */
    protected $is_required;
    /** @var bool */
    protected $disabled;

    /**
     * CRUDFormWidgetRadios constructor.
     * @param string $field_name
     * @param array $options_arr
     * @param bool $show_null_checkbox
     * @param bool $is_required
     * @param bool $disabled
     */
    public function __construct(
        string $field_name,
        array $options_arr,
        bool $show_null_checkbox = false,
        bool $is_required = false,
        bool $disabled = false
    ) {
        $this->setFieldName($field_name);
        $this->setOptionsArr($options_arr);
        $this->setShowNullCheckbox($show_null_checkbox);
        $this->setIsRequired($is_required);
        $this->setDisabled($disabled);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        return $this->htmlForValue($field_value);
    }

    /**
     * @param string|null $field_value
     * @param string|null $input_name
     * @return string
     */
    public function htmlForValue(?string $field_value = null, ?string $input_name = null): string
    {
        $field_name = $this->getFieldName();

        if (is_null($input_name)) {
            $input_name = $field_name;
        }

        $uniqid = uniqid('CRUDFormWidgetRadios_');
        $input_cols = $this->isShowNullCheckbox() ? '10' : '12';

        $html = '';
        //$html .= '<div class="row">';
        //$html .= '<div class="col-sm-' . $input_cols . '" id="' . $uniqid . '_radio_box">';
        $html .= '<div id="' . $uniqid . '_radio_box">';

        $options_arr = $this->getOptionsArr();

        $disabled = '';
        if ($this->isDisabled()) {
            $disabled = 'disabled';
        }

        foreach ($options_arr as $value => $title) {
            $selected_html_attr = '';
            if (!is_null($field_value) && $field_value == $value) {
                $selected_html_attr = ' checked ';
            }

            $is_required_str = '';
            if ($this->is_required) {
                $is_required_str = ' required ';
            }

            $html .= '<label class="radio-inline"><input type="radio" name="' .
                Sanitize::sanitizeAttrValue($input_name) . '" value="' . Sanitize::sanitizeAttrValue($value) . '" ' .
                $selected_html_attr . ' ' . $is_required_str . ' ' . $disabled . '> ' . $title . '</label>';
        }
        //$html .= '</div>';

        if ($this->isShowNullCheckbox()) {
            $is_null_checked = '';
            if (is_null($field_value)) {
                $is_null_checked = ' checked ';
            }
            ob_start(); ?>
            <label class="radio-inline">
                <input id="<?= $uniqid ?>___is_null" type="checkbox" value="1"
                       name="<?= Sanitize::sanitizeAttrValue($input_name) ?>___is_null" <?= $is_null_checked ?>>
                Нет значения
            </label>
            <script>
                (function () {
                    var $input_is_null = $('#<?= $uniqid ?>___is_null');
                    var $input = $('#<?= $uniqid ?>_radio_box').find('input[type="radio"]');

                    $input.on('change', function () {
                        $input_is_null.prop('checked', false);
                    });

                    $input_is_null.on('change', function () {
                        if ($(this).is(':checked')) {
                            $input.prop('checked', false);
                        }
                    });
                })();
            </script>
            <?php
            $html .= ob_get_clean();
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
