<?php

namespace WebSK\CRUD\Form\Widgets;

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
    const ACE_MODE_HTML = 'ace/mode/html';
    const ACE_MODE_JAVASCRIPT = 'ace/mode/javascript';
    const ACE_MODE_PHP = 'ace/mode/php';
    const ACE_MODE_SQL = 'ace/mode/sql';
    const ACE_MODE_XML = 'ace/mode/xml';
    const ACE_MODE_TEXT = 'ace/mode/text';

    /** @var string */
    protected $field_name;

    /** @var string */
    protected $ace_mode = self::ACE_MODE_HTML;

    /** @var bool */
    protected $ace_use_worker = false;

    /**
     * CRUDFormWidgetAceTextarea constructor.
     * @param string $field_name
     * @param string $ace_mode
     * @param bool $ace_user_worker
     */
    public function __construct(string $field_name, $ace_mode = self::ACE_MODE_HTML, $ace_user_worker = false)
    {
        $this->setFieldName($field_name);
        $this->setAceMode($ace_mode);
        $this->setAceUseWorker($ace_user_worker);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        $editor_element_id = 'editor_' . time() . '_' . rand(1, 999999);
        $html = '';

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

        // @TODO: multiple insertion!!!!
        $html .= '<script src="/assets/libraries/ace/ace.js" '.
            'type="text/javascript" charset="utf-8"></script>
            <script>
            var editor = ace.edit("' . $editor_element_id . '");
            editor.setOption("maxLines", "Infinity");
            
            editor.getSession().setMode(' . json_encode($this->getAceMode()) . ');
            editor.getSession().setUseWorker(' . json_encode($this->getAceUseWorker()) . ');

            editor.getSession().on("change", function() {
                var target = document.getElementById("' . $editor_element_id . '_target");
                target.innerHTML = editor.getSession().getValue();
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
    public function getAceUseWorker(): bool
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
}
