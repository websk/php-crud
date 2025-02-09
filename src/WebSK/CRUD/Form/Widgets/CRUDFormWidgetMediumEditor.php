<?php

namespace WebSK\CRUD\Form\Widgets;

use WebSK\Entity\InterfaceEntity;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;

/**
 * Class CRUDFormWidgetMediumEditor
 * @package WebSK\CRUD
 */
class CRUDFormWidgetMediumEditor implements InterfaceCRUDFormWidget
{
    protected string $field_name;

    protected string $uniqid = '';

    protected string $editor_options_str = 'placeholder: false';

    /**
     * CRUDFormWidgetMediumEditor constructor.
     * @param string $field_name
     * @param string $uniqid
     * @param string $editor_options_str
     */
    public function __construct(
        string $field_name,
        string $uniqid = '',
        string $editor_options_str = 'placeholder: false'
    )
    {
        $this->setFieldName($field_name);
        $this->setEditorOptionsStr($editor_options_str);

        if ($uniqid) {
            $this->setUniqid($uniqid);
        } else {
            $this->setUniqid(uniqid('CRUDFormWidgetMediumEditor_'));
        }
    }

    /**
     * @param InterfaceEntity $entity_obj
     * @param CRUD $crud
     * @return string
     * @throws \ReflectionException
     */
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string
    {
        static $CRUDFormWidgetMediumEditor_include_script;

        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($entity_obj, $field_name);

        /* @TODO Нужно изменить на нах CDN */
        $script = '';
        $uniqid = $this->getUniqid();
        if (!isset($CRUDFormWidgetMediumEditor_include_script)) {
            $script = '
                <script src="https://cdnjs.cloudflare.com/ajax/libs/medium-editor/5.23.3/js/medium-editor.min.js"></script>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/medium-editor/5.23.3/css/medium-editor.min.css" type="text/css" media="screen" charset="utf-8"/>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/medium-editor/5.23.3/css/themes/default.min.css" type="text/css" media="screen" charset="utf-8"/>

				<style>
				.medium-editor-element {
                   min-height: 300px;
                }				
                </style>
			';
            $CRUDFormWidgetMediumEditor_include_script = false;
        }

        $html = '';

        $html .= '<textarea id="' . $uniqid . '_textarea" name="' . Sanitize::sanitizeAttrValue($field_name) . '" '.
            'style="display: none;">' . $field_value . '</textarea>';
        $html .= '<div id="' . $uniqid . '" class="form-control" style="height: auto;">' . $field_value . '</div>';


        ob_start(); ?>
        <script>
            var <?= $uniqid ?> = new MediumEditor("#<?= $uniqid ?>", { <?= $this->getEditorOptionsStr() ?>});

            <?= $uniqid ?>.subscribe('editableInput', function (event, editable) {
                var content = $(editable).html();
                $('#<?= $uniqid ?>_textarea').val(content).trigger('MediumEditor.change');
            });
        </script>
        <?php
        $html .= ob_get_clean();

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
     * @return string
     */
    public function getUniqid(): string
    {
        return $this->uniqid;
    }

    /**
     * @param string $uniqid
     */
    public function setUniqid(string $uniqid): void
    {
        $this->uniqid = $uniqid;
    }

    /**
     * @return string
     */
    public function getEditorOptionsStr(): string
    {
        return $this->editor_options_str;
    }

    /**
     * @param string $editor_options_str
     */
    public function setEditorOptionsStr(string $editor_options_str): void
    {
        $this->editor_options_str = $editor_options_str;
    }
}
