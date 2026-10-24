<?php

namespace WebSK\CRUD\Form\Widgets;

use WebSK\Entity\InterfaceEntity;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\CRUDCompiler;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;

/**
 * Class CRUDFormWidgetDate
 * @package WebSK\CRUD
 */
class CRUDFormWidgetDate implements InterfaceCRUDFormWidget
{
    protected string $field_name;

    protected bool $show_null_checkbox = false;

    protected bool $is_required = false;

    /**
     * CRUDFormWidgetDate constructor.
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

    /**
     * @param InterfaceEntity $entity_obj
     * @param CRUD $crud
     * @return string
     * @throws \ReflectionException
     */
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($entity_obj, $field_name);
        $field_value = CRUDCompiler::nullableString($field_value, $field_name);

        $is_required_str = '';
        if ($this->is_required) {
            $is_required_str = ' required ';
        }

        $field_value_attr = '';
        if ($field_value) {
            $timestamp = strtotime($field_value);
            if ($timestamp === false) {
                throw new \UnexpectedValueException($field_name . ' contains an invalid date');
            }
            $field_value_attr = date('Y-m-d', $timestamp);
        }

        $uniqid = uniqid('CRUDFormWidgetDate_');
        $input_cols = $this->isShowNullCheckbox() ? '10' : '12';

        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-sm-' . $input_cols . '">';

        ob_start(); ?>
        <input id="<?= $uniqid ?>_date" type="date" class="form-control"
               name="<?= Sanitize::sanitizeAttrValue($field_name) ?>"
               value="<?= Sanitize::sanitizeAttrValue($field_value_attr) ?>" <?= $is_required_str ?>>
        <?php
        $html .= ob_get_clean() ?: '';
        $html .= '</div>';

        if ($this->isShowNullCheckbox()) {
            $is_null_checked = '';
            if (is_null($field_value)) {
                $is_null_checked = ' checked ';
            }
            ob_start(); ?>
            <div class="col-sm-2 d-flex align-items-center">
                <div class="form-check">
                    <input class="form-check-input" id="<?= $uniqid ?>___is_null" type="checkbox" value="1"
                           name="<?= Sanitize::sanitizeAttrValue($field_name) ?>___is_null" <?= $is_null_checked ?>>
                    <label class="form-check-label" for="<?= $uniqid ?>___is_null">Нет значения</label>
                </div>
            </div>
            <script>
                (function () {
                    var $input_is_null = $('#<?= $uniqid ?>___is_null');

                    $("#<?= $uniqid ?>_date").on('change', function () {
                        if ($(this).val() != '') {
                            $input_is_null.prop('checked',false);
                        }
                    });

                    $input_is_null.on('change', function () {
                        if ($(this).is(':checked')) {
                            $('#<?= $uniqid ?>_date').val('').trigger('change');
                        }
                    });
                })();
            </script>
            <?php
            $html .= ob_get_clean() ?: '';
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
