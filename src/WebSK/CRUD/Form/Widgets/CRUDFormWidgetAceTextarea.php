<?php

namespace WebSK\CRUD\Form\Widgets;

use WebSK\Entity\InterfaceEntity;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;

/**
 * Class CRUDFormWidgetAceTextarea
 * @package WebSK\CRUD
 */
class CRUDFormWidgetAceTextarea implements InterfaceCRUDFormWidget
{
    const string ACE_MODE_HTML = 'ace/mode/html';
    const string ACE_MODE_JAVASCRIPT = 'ace/mode/javascript';
    const string ACE_MODE_JSON = 'ace/mode/json';
    const string ACE_MODE_PHP = 'ace/mode/php';
    const string ACE_MODE_SQL = 'ace/mode/sql';
    const string ACE_MODE_XML = 'ace/mode/xml';
    const string ACE_MODE_TEXT = 'ace/mode/text';

    protected string $field_name;

    protected string  $ace_mode = self::ACE_MODE_HTML;

    protected bool $ace_use_worker = false;

    protected bool $wrap_text = false;

    /**
     * CRUDFormWidgetAceTextarea constructor.
     * @param string $field_name
     * @param string $ace_mode
     * @param bool $ace_user_worker
     * @param bool $wrap_text
     */
    public function __construct(
        string $field_name,
        string $ace_mode = self::ACE_MODE_HTML,
        $ace_user_worker = false,
        $wrap_text = false
    )
    {
        $this->setFieldName($field_name);
        $this->setAceMode($ace_mode);
        $this->setAceUseWorker($ace_user_worker);
        $this->setWrapText($wrap_text);
    }

    /**
     * @param InterfaceEntity $entity_obj
     * @param CRUD $crud
     * @return string
     * @throws \ReflectionException
     */
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string
    {
        static $CRUDFormWidgetAceTextarea_include_script;

        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($entity_obj, $field_name);

        $html = '';

        if (!isset($CRUDFormWidgetAceTextarea_include_script)) {
            $html .= '<script src="/assets/libraries/ace/ace.js" type="text/javascript" charset="utf-8"></script>';
            $CRUDFormWidgetAceTextarea_include_script = false;
        }

        $editor_element_id = 'editor_' . time() . '_' . Sanitize::sanitizeAttrValue($field_name) . rand(1, 999999);

        $html .= '
            <style>
             #' . $editor_element_id . ' {
                position: relative;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                min-height: 500px;
            }
            </style>';

        // @TODO: is form-control needed?
        $html .= '<div id="' . $editor_element_id . '" class="form-control">' .
            Sanitize::sanitizeTagContent($field_value) . '</div>';
        $html .= '<textarea id="' . $editor_element_id . '_target" name="' . Sanitize::sanitizeAttrValue($field_name) .
            '" style="display: none;">' . Sanitize::sanitizeTagContent($field_value) . '</textarea>';

        $html .= '<script>
            var editor' . $editor_element_id . ' = ace.edit("' . $editor_element_id . '");
            editor' . $editor_element_id . '.setOption("maxLines", "Infinity");
            editor' . $editor_element_id . '.setOption("wrap", ' . json_encode($this->isWrapText()) . ');
            
            editor' . $editor_element_id . '.getSession().setMode(' . json_encode($this->getAceMode()) . ');
            editor' . $editor_element_id . '.getSession().setUseWorker(' . json_encode($this->isAceUseWorker()) . ');

            editor' . $editor_element_id . '.getSession().on("change", function() {
                var target = document.getElementById("' . $editor_element_id . '_target");
                target.innerHTML = editor' . $editor_element_id . '.getSession().getValue();
            });
            </script>';

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
    public function getAceMode(): string
    {
        return $this->ace_mode;
    }

    /**
     * @param string $ace_mode
     */
    public function setAceMode(string $ace_mode): void
    {
        $this->ace_mode = $ace_mode;
    }

    /**
     * @return bool
     */
    public function isAceUseWorker(): bool
    {
        return $this->ace_use_worker;
    }

    /**
     * @param bool $ace_use_worker
     */
    public function setAceUseWorker(bool $ace_use_worker): void
    {
        $this->ace_use_worker = $ace_use_worker;
    }

    /**
     * @return bool
     */
    public function isWrapText(): bool
    {
        return $this->wrap_text;
    }

    /**
     * @param bool $wrap_text
     */
    public function setWrapText(bool $wrap_text): void
    {
        $this->wrap_text = $wrap_text;
    }
}
