<?php

namespace WebSK\CRUD\Form\Widgets;

use WebSK\CRUD\Form\CRUDFormScript;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;

/**
 * Class CRUDFormWidgetDateTime
 * @package WebSK\CRUD
 */
class CRUDFormWidgetDateTime implements InterfaceCRUDFormWidget
{
    protected string $field_name;

    protected bool $show_null_checkbox = false;

    protected bool $is_required = false;

    /**
     * CRUDFormWidgetDateTime constructor.
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
    public function html($entity_obj, CRUD $crud): string
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($entity_obj, $field_name);

        $is_required_str = '';
        if ($this->is_required) {
            $is_required_str = ' required ';
        }

        $field_value_attr = '';
        if ($field_value) {
            $field_value_attr = date('d-m-Y H:i:s', strtotime($field_value));
        }

        $uniqid = uniqid('CRUDFormWidgetDateTime_');
        $input_cols = $this->isShowNullCheckbox() ? '10' : '12';

        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-sm-' . $input_cols . '">';

        ob_start(); ?>
        <input type="hidden" id="<?= $uniqid ?>_input" name="<?= Sanitize::sanitizeAttrValue($field_name) ?>"
               value="<?= Sanitize::sanitizeTagContent($field_value) ?>"
               data-field="<?= $uniqid ?>_date" <?= $is_required_str ?>>
        <div class="input-group date" id="<?= $uniqid ?>">
            <input id="<?= $uniqid ?>_date" type="text" class="form-control" value="<?= $field_value_attr ?>">
            <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
        </div>
        <script>
            $("#<?= $uniqid ?>").datetimepicker({
                format: "DD-MM-YYYY HH:mm:ss",
                sideBySide: true,
                showTodayButton: true
            }).on("dp.change", function (obj) {
                if (obj.date) {
                    $("#<?= $uniqid ?>_input").val(obj.date.format("YYYY-MM-DD HH:mm:ss")).trigger('change');
                } else {
                    $("#<?= $uniqid ?>_input").val('').trigger('change');
                }
            });
        </script>
        <?php
        $html .= ob_get_clean();
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

                    $("#<?= $uniqid ?>_input").on('change', function () {
                        if ($(this).val() != '') {
                            $input_is_null.prop('checked', false);
                        }
                    });

                    $input_is_null.on('change', function () {
                        if ($(this).is(':checked')) {
                            $('#<?= $uniqid ?>').data("DateTimePicker").clear();
                            $('#<?= $uniqid ?>_input').val('');
                        }
                    });
                })();
            </script>
            <?php
            $html .= ob_get_clean();
        }

        $html .= '</div>';

        $script = CRUDFormScript::includeBootstrapDateTimeScripts();

        return $script . $html;
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
