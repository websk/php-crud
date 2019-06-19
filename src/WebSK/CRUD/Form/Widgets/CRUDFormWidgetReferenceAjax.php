<?php

namespace WebSK\CRUD\Form\Widgets;

use OLOG\Preloader;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;

class CRUDFormWidgetReferenceAjax implements InterfaceCRUDFormWidget
{
    const REFERENCED_ID_PLACEHOLDER = 'REFERENCED_ID';

    /** @var string */
    protected $field_name;
    /** @var string */
    protected $ajax_action_url;
    /** @var string */
    protected $referenced_class_name;
    /** @var string */
    protected $referenced_class_title_field;
    /** @var string */
    protected $editor_url;
    /** @var bool */
    protected $is_required;

    public function __construct(
        string $field_name,
        string $referenced_class_name,
        string $referenced_class_title_field,
        string $ajax_action_url,
        string $editor_url,
        bool $is_required = false
    ) {
        $this->setFieldName($field_name);
        $this->setAjaxActionUrl($ajax_action_url);
        $this->setReferencedClassName($referenced_class_name);
        $this->setReferencedClassTitleField($referenced_class_title_field);
        $this->setEditorUrl($editor_url);
        $this->setIsRequired($is_required);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        $field_name = $this->getFieldName();
//        if (is_null($input_name)) {
//            $input_name = $field_name;
//        }
        $input_name = $field_name;

        $referenced_class_name = $this->getReferencedClassName();
        $referenced_class_title_field = $this->getReferencedClassTitleField();

        $referenced_obj_title = '';
        $disabled_btn_link = 'disabled';
        $is_null_value = '';

        if (is_null($field_value)) {
            $is_null_value = "1";
        }

        if (!is_null($field_value)) {
            $referenced_obj = $crud->createAndLoadObject($referenced_class_name, $field_value);
            $referenced_obj_title = CRUDFieldsAccess::getObjectFieldValue(
                $referenced_obj,
                $referenced_class_title_field
            );
            $disabled_btn_link = '';
        }

        $is_required_str = '';
        if ($this->isRequired()) {
            $is_required_str = ' required ';
        }

        $html = '';
        $html .= Preloader::preloaderJsHtml();

        $select_element_id = 'js_select_' . rand(1, 999999);
        $choose_form_element_id = 'collapse_' . rand(1, 999999);

        $html .= '<input type="hidden" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '" ' .
            'name="' . Sanitize::sanitizeAttrValue($input_name) . '" value="' . $field_value . '" ' .
            'data-field="' . Sanitize::sanitizeAttrValue($select_element_id) . '_text" ' . $is_required_str . '/>';
        $html .= '<input type="hidden" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_is_null" ' .
            'name="' . Sanitize::sanitizeAttrValue($input_name) . '___is_null" value="' . $is_null_value . '"/>';

        $html .= '<div class="input-group">';

        if ($this->getAjaxActionUrl()) {
            $html .= '<span class="input-group-btn">';
            $html .= '<button type="button" class="btn btn-default" data-toggle="modal" data-target="#' .
                $choose_form_element_id . '"><span class="glyphicon glyphicon-folder-open"></span></button>';
            $html .= '<button type="button" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_btn_is_null" ' .
                'class="btn btn-default"><span class="fa fa-times fa-lg text-danger fa-fw"></span></button>';
            $html .= '</span>';
        }

        $html .= '<div class="form-control" style="overflow: auto;" '.
            'id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_text">' . $referenced_obj_title . '</div>';

        if ($this->getEditorUrl()) {
            $html .= '<span class="input-group-btn">';
            $html .= '<button ' . $disabled_btn_link . ' type="button" '.
                'id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_btn_link" '.
                'class="btn btn-link">Перейти</button>';
            $html .= '</span>';
        }

        $html .= '</div>';

        $html .= self::modal($choose_form_element_id, 'Выбрать');

        ob_start(); ?>

        <script>
            $('#<?= $choose_form_element_id ?>').on('hidden.bs.modal', function () {
                $('#<?= $choose_form_element_id ?> .modal-body').html('');
            });

            $('#<?= $choose_form_element_id ?>').on('shown.bs.modal', function (e) {
                OLOG.preloader.show();
                $.ajax({
                    url: "<?= $this->getAjaxActionUrl() ?>"
                }).success(function (received_html) {
                    $('#<?= $choose_form_element_id ?> .modal-body').html(received_html);
                    OLOG.preloader.hide();
                }).error(function (err) {
                    $('#<?= $choose_form_element_id ?> .modal-body')
                        .html('<div class="alert alert-danger">' + err.status + ': ' + err.statusText + '</div>');
                    OLOG.preloader.hide();
                });
            });

            $('#<?= $choose_form_element_id ?>').on('click', '.js-ajax-form-select', function (e) {
                e.preventDefault();
                var select_id = $(this).data('id');
                var select_title = $(this).data('title');
                $('#<?= $choose_form_element_id ?>').modal('hide');
                $('#<?= $select_element_id ?>_text').text(select_title);
                $('#<?= $select_element_id ?>_btn_link').attr('disabled', false);
                $('#<?= $select_element_id ?>').val(select_id).trigger('change');
                $('#<?= $select_element_id ?>_is_null').val('');
            });

            $('#<?= $select_element_id ?>_btn_is_null').on('click', function (e) {
                e.preventDefault();
                $('#<?= $select_element_id ?>_text').text('');
                $('#<?= $select_element_id ?>_btn_link').attr('disabled', true);
                $('#<?= $select_element_id ?>').val('').trigger('change');
                $('#<?= $select_element_id ?>_is_null').val(1);
            });

            $('#<?= $select_element_id ?>_btn_link').on('click', function (e) {
                var url = '<?= $this->getEditorUrl() ?>';
                var id = $('#<?= $select_element_id ?>').val();
                url = url.replace('<?= self::REFERENCED_ID_PLACEHOLDER ?>', id);

                window.location = url;
            });
        </script>

        <?php
        $html .= ob_get_clean();

        return $html;
    }

    /**
     * @param $modal_element_id
     * @param $title
     * @param string $contents_html
     * @return string
     */
    protected static function modal($modal_element_id, $title, $contents_html = ''){
        $html = '<div class="modal fade" id="' . $modal_element_id . '" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
		</button>
		<h4 class="modal-title">' . $title . '</h4>
		</div>
		<div class="modal-body">' . $contents_html . '</div>
		</div>
		</div>
		</div>';

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
    public function getAjaxActionUrl(): string
    {
        return $this->ajax_action_url;
    }

    /**
     * @param string $ajax_action_url
     */
    public function setAjaxActionUrl(string $ajax_action_url): void
    {
        $this->ajax_action_url = $ajax_action_url;
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

    /**
     * @return string
     */
    public function getEditorUrl(): string
    {
        return $this->editor_url;
    }

    /**
     * @param string $editor_url
     */
    public function setEditorUrl(string $editor_url): void
    {
        $this->editor_url = $editor_url;
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
