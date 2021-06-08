<?php

namespace WebSK\CRUD\Form\Widgets;

use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;

/**
 * Class CRUDFormWidgetInput
 * @package WebSK\CRUD
 */
class CRUDFormWidgetInput implements InterfaceCRUDFormWidget
{
    protected string $field_name;

    protected bool $show_null_checkbox = false;

    protected bool $is_required = false;

    protected bool $disabled = false;

    protected bool $disable_autocomplete = false;

    /**
     * CRUDFormWidgetInput constructor.
     * @param string $field_name
     * @param bool $show_null_checkbox
     * @param bool $is_required
     * @param bool $disabled
     * @param bool $disable_autocomplete
     */
    public function __construct(
        string $field_name,
        bool $show_null_checkbox = false,
        bool $is_required = false,
        bool $disabled = false,
        bool $disable_autocomplete = false
    )
    {
        $this->setFieldName($field_name);
        $this->setShowNullCheckbox($show_null_checkbox);
        $this->setIsRequired($is_required);
        $this->setDisabled($disabled);
        $this->setDisableAutocomplete($disable_autocomplete);
    }

    /** @inheritdoc */
    public function html($entity_obj, CRUD $crud): string
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($entity_obj, $field_name);

        $is_required_str = '';
        if ($this->is_required) {
            $is_required_str = ' required ';
        }

        $uniqid = uniqid('CRUDFormWidgetInput_');
        $input_cols = $this->isShowNullCheckbox() ? '10' : '12';

        $disabled = '';
        if ($this->isDisabled()) {
            $disabled = ' disabled';
        }

        $disable_autocomplete = '';
        if ($this->isDisableAutocomplete()) {
            $disable_autocomplete = ' autocomplete="off"';
        }

        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-sm-' . $input_cols . '">';
        $html .= '<input id="' . $uniqid . '_input" name="' . Sanitize::sanitizeAttrValue($field_name) . '" ' .
            $is_required_str .
            $disabled .
            $disable_autocomplete .
            ' class="form-control" value="' . Sanitize::sanitizeAttrValue($field_value) . '"/>';
        $html .= '</div>';

        if ($this->isShowNullCheckbox()) {
            $is_null_checked = '';
            if (is_null($field_value)) {
                $is_null_checked = ' checked ';
            }
            ob_start(); ?>
            <div class="col-sm-2">
                <label class="form-control-static">
                    <input id="<?= $uniqid ?>___is_null" type="checkbox" value="1"
                           name="<?= Sanitize::sanitizeAttrValue($field_name) ?>___is_null" <?= $is_null_checked ?>>
                    Нет значения
                </label>
            </div>
            <script>
                (function () {
                    var $input_is_null = $('#<?= $uniqid ?>___is_null');
                    var $input = $('#<?= $uniqid ?>_input');

                    $input.on('change keydown', function () {
                        $input_is_null.prop('checked', false);
                    });

                    $input_is_null.on('change', function () {
                        if ($(this).is(':checked')) {
                            $input.val('');
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

    /**
     * @return bool
     */
    public function isDisableAutocomplete(): bool
    {
        return $this->disable_autocomplete;
    }

    /**
     * @param bool $disable_autocomplete
     */
    public function setDisableAutocomplete(bool $disable_autocomplete): void
    {
        $this->disable_autocomplete = $disable_autocomplete;
    }
}
