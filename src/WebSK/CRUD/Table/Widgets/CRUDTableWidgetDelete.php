<?php

namespace WebSK\CRUD\Table\Widgets;

use WebSK\CRUD\CRUDOperations;
use WebSK\Entity\InterfaceEntity;
use WebSK\Utils\Assert;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetDelete
 * @package WebSK\CRUD\Table\Widgets
 */
class CRUDTableWidgetDelete implements InterfaceCRUDTableWidget
{
    const string FIELD_CLASS_NAME = '_class_name';
    const string FIELD_OBJECT_ID = '_id';
    const string FIELD_REDIRECT_AFTER_DELETE_URL = 'redirect_after_delete_url';

    protected string $redirect_after_delete_url;

    protected string $button_text = '';

    protected string $button_class_str = '';
    
    protected string $form_action_url = '';
    
    /**
     * CRUDTableWidgetDelete constructor.
     * @param string $button_text
     * @param string $button_class_str
     * @param string $redirect_after_delete_url
     * @param string $form_action_url
     */
    public function __construct(
        string $button_text = '',
        string $button_class_str = 'btn btn-default btn-sm',
        string $redirect_after_delete_url = '',
        string $form_action_url = ''
    ) {
        $this->setButtonClassStr($button_class_str);
        $this->setButtonText($button_text);
        $this->setRedirectAfterDeleteUrl($redirect_after_delete_url);
        $this->setFormActionUrl($form_action_url);
    }

    /** @inheritdoc */
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string
    {
        Assert::assert($entity_obj);

        $content_html = '';
        $content_html .= '<form style="display: inline;" method="post"' . ($this->getFormActionUrl() ? ' action=" ' . $this->getFormActionUrl() .  '"' : '') . '>';
        $content_html .= '<input type="hidden" name="' . CRUDOperations::FIELD_NAME_OPERATION_CODE . '" value="' . Sanitize::sanitizeAttrValue(CRUDOperations::OPERATION_DELETE_ENTITY) . '">';
        $content_html .= '<input type="hidden" name="' . self::FIELD_CLASS_NAME . '" ' .
            'value="' . Sanitize::sanitizeAttrValue(get_class($entity_obj)) . '">';
        $content_html .= '<input type="hidden" name="' . self::FIELD_OBJECT_ID . '" ' .
            'value="' . Sanitize::sanitizeAttrValue(CRUDFieldsAccess::getObjId($entity_obj)) . '">';

        if ($this->getRedirectAfterDeleteUrl() != '') {
            $content_html .= '<input type="hidden" name="' . self::FIELD_REDIRECT_AFTER_DELETE_URL . '" ' .
                'value="' . Sanitize::sanitizeAttrValue($this->getRedirectAfterDeleteUrl()) . '">';
        }

        $content_html .= '<button class="' . Sanitize::sanitizeAttrValue($this->getButtonClassStr()) . '" ' .
            'type="submit" onclick="return window.confirm(\'Удалить?\');"><span class="fa fa-trash fa-lg text-danger fa-fw"></span>' .
            Sanitize::sanitizeTagContent($this->getButtonText()) . '</button>';

        $content_html .= '</form>';

        return $content_html;
    }

    /**
     * @return string
     */
    public function getRedirectAfterDeleteUrl(): string
    {
        return $this->redirect_after_delete_url;
    }

    /**
     * @param string $redirect_after_delete_url
     */
    public function setRedirectAfterDeleteUrl(string $redirect_after_delete_url): void
    {
        $this->redirect_after_delete_url = $redirect_after_delete_url;
    }

    /**
     * @return string
     */
    public function getButtonText(): string
    {
        return $this->button_text;
    }

    /**
     * @param string $button_text
     */
    public function setButtonText(string $button_text): void
    {
        $this->button_text = $button_text;
    }

    /**
     * @return string
     */
    public function getButtonClassStr(): string
    {
        return $this->button_class_str;
    }

    /**
     * @param string $button_class_str
     */
    public function setButtonClassStr(string $button_class_str): void
    {
        $this->button_class_str = $button_class_str;
    }

    /**
     * @return string
     */
    public function getFormActionUrl(): string
    {
        return $this->form_action_url;
    }

    /**
     * @param string $form_action_url
     */
    public function setFormActionUrl(string $form_action_url): void
    {
        $this->form_action_url = $form_action_url;
    }    
}
