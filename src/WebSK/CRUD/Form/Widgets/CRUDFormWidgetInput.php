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
    /** @var string */
    protected $field_name;
    /** @var bool */
    protected $show_null_checkbox;
    /** @var bool */
    protected $is_required;

    /**
     * CRUDFormWidgetInput constructor.
     * @param string $field_name
     * @param bool $show_null_checkbox
     * @param bool $is_required
     */
    public function __construct(string $field_name, bool $show_null_checkbox = false, bool $is_required = false)
    {
        $this->setFieldName($field_name);
        $this->setShowNullCheckbox($show_null_checkbox);
        $this->setIsRequired($is_required);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        $is_required_str = '';
        if ($this->is_required) {
            $is_required_str = ' required ';
        }

        $uniqid = uniqid('CRUDFormWidgetInput_');
        $input_cols = $this->isShowNullCheckbox() ? '10' : '12';

        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-sm-' . $input_cols . '">';
        $html .= '<input id="' . $uniqid . '_input" name="' . Sanitize::sanitizeAttrValue($field_name) . '" ' .
            $is_required_str . ' class="form-control" value="' . Sanitize::sanitizeAttrValue($field_value) . '"/>';
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
}
