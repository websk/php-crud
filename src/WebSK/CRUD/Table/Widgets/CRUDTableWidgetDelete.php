<?php

namespace WebSK\CRUD\Table\Widgets;

use WebSK\Utils\Assert;
use OLOG\Operations;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

class CRUDTableWidgetDelete implements InterfaceCRUDTableWidget
{
    const FIELD_CLASS_NAME = '_class_name';
    const FIELD_OBJECT_ID = '_id';
    const FIELD_REDIRECT_AFTER_DELETE_URL = 'redirect_after_delete_url';

    /** @var string */
    protected $redirect_after_delete_url;
    /** @var string */
    protected $button_text;
    /** @var string */
    protected $button_class_str;

    /**
     * CRUDTableWidgetDelete constructor.
     * @param string $button_text
     * @param string $button_class_str
     * @param string $redirect_after_delete_url
     */
    public function __construct(
        string $button_text = '',
        string $button_class_str = 'btn btn-xs btn-default btn-sm fa fa-trash-o fa-lg text-danger fa-fw',
        string $redirect_after_delete_url = ''
    ) {
        $this->setButtonClassStr($button_class_str);
        $this->setButtonText($button_text);
        $this->setRedirectAfterDeleteUrl($redirect_after_delete_url);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        Assert::assert($obj);

        $o = '';
        $o .= '<form style="display: inline;" method="post">';
        $o .= Operations::operationCodeHiddenField(CRUDTable::OPERATION_DELETE_ENTITY);
        $o .= '<input type="hidden" name="' . self::FIELD_CLASS_NAME . '" ' .
            'value="' . Sanitize::sanitizeAttrValue(get_class($obj)) . '">';
        $o .= '<input type="hidden" name="' . self::FIELD_OBJECT_ID . '" ' .
            'value="' . Sanitize::sanitizeAttrValue(CRUDFieldsAccess::getObjId($obj)) . '">';

        if ($this->getRedirectAfterDeleteUrl() != '') {
            $o .= '<input type="hidden" name="' . self::FIELD_REDIRECT_AFTER_DELETE_URL . '" ' .
                'value="' . Sanitize::sanitizeAttrValue($this->getRedirectAfterDeleteUrl()) . '">';
        }

        $o .= '<button class="' . Sanitize::sanitizeAttrValue($this->getButtonClassStr()) . '" ' .
            'type="submit" onclick="return window.confirm(\'Удалить?\');">' .
            Sanitize::sanitizeTagContent($this->getButtonText()) . '</button>';

        $o .= '</form>';

        return $o;
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
}
