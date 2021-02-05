<?php

namespace WebSK\CRUD\Form\Widgets;

use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;

/**
 * Class CRUDFormWidgetReference
 * @package WebSK\CRUD\Form\Widgets
 */
class CRUDFormWidgetReference implements InterfaceCRUDFormWidget
{
    protected string $field_name;

    protected string $referenced_class_name;

    protected string $referenced_class_title_field;

    /**
     * CRUDFormWidgetReference constructor.
     * @param string $field_name
     * @param string $referenced_class_name
     * @param string $referenced_class_title_field
     */
    public function __construct(string $field_name, string $referenced_class_name, string $referenced_class_title_field)
    {
        $this->setFieldName($field_name);
        $this->setReferencedClassName($referenced_class_name);
        $this->setReferencedClassTitleField($referenced_class_title_field);
    }

    /** @inheritdoc */
    public function html($entity_obj, CRUD $crud): string
    {
        $field_name = $this->getFieldName();
        $referenced_class_name = $this->getReferencedClassName();
        $referenced_class_title_field = $this->getReferencedClassTitleField();

        $field_value = CRUDFieldsAccess::getObjectFieldValue($entity_obj, $field_name);

        $options_html_arr = ['<option value=""></option>'];

        $referenced_obj_ids_arr = $crud->getAllIdsArr($referenced_class_name);

        $options_arr = [];
        foreach ($referenced_obj_ids_arr as $id) {
            $entity_obj = $crud->createAndLoadObject($referenced_class_name, $id);
            $options_arr[$id] = CRUDFieldsAccess::getObjectFieldValue($entity_obj, $referenced_class_title_field);
        }

        foreach ($options_arr as $value => $title) {
            $selected_html_attr = '';
            if ($field_value == $value) {
                $selected_html_attr = ' selected';
            }

            $options_html_arr[] = '<option value="' . $value . '" ' . $selected_html_attr . '>' . Sanitize::sanitizeTagContent($title) . '</option>';
        }

        $html = '';

        $select_element_id = 'js_select_' . rand(1, 999999);

        $html .= '<select id="' . Sanitize::sanitizeAttrValue($select_element_id) . '" ' .
            'name="' . Sanitize::sanitizeAttrValue($field_name) . '" class="form-control">' .
            implode('', $options_html_arr) . '</select>';
        $html .= '<input type="hidden" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_is_null" ' .
            'name="' . Sanitize::sanitizeAttrValue($field_name) . '___is_null"/>';

        ob_start(); ?>
        <script>
            var select_element = document.getElementById('<?= $select_element_id ?>');
            select_element.addEventListener(
                'change',
                function () {
                    var select_element_id = document.getElementById('<?= $select_element_id ?>');
                    var is_null_element = document.getElementById('<?= $select_element_id ?>_is_null');
                    var value = select_element_id.options[select_element_id.selectedIndex].value;

                    if (value == '') {
                        is_null_element.value = '1';
                    } else {
                        is_null_element.value = '';
                    }
                }
            );

            select_element.dispatchEvent(new Event('change')); // fire to initialize is_null input on display
        </script>

        <?php
        $html .= ob_get_clean();

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
     * @return string
     */
    public function getReferencedClassName(): string
    {
        return $this->referenced_class_name;
    }

    /**
     * @param string $referenced_class_name
     */
    public function setReferencedClassName(string $referenced_class_name): void
    {
        $this->referenced_class_name = $referenced_class_name;
    }

    /**
     * @return string
     */
    public function getReferencedClassTitleField(): string
    {
        return $this->referenced_class_title_field;
    }

    /**
     * @param string $referenced_class_title_field
     */
    public function setReferencedClassTitleField(string $referenced_class_title_field): void
    {
        $this->referenced_class_title_field = $referenced_class_title_field;
    }
}
