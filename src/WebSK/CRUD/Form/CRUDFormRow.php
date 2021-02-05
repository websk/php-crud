<?php

namespace WebSK\CRUD\Form;

use WebSK\CRUD\CRUD;
use WebSK\Entity\InterfaceEntity;

/**
 * Class CRUDFormRow
 * @package WebSK\CRUD
 */
class CRUDFormRow implements InterfaceCRUDFormRow
{
    protected string $title;

    protected InterfaceCRUDFormWidget $widget_obj;

    protected string $comment_str;

    /**
     * CRUDFormRow constructor.
     * @param string $title
     * @param InterfaceCRUDFormWidget $widget_obj
     * @param string $comment_str
     */
    public function __construct(string $title, InterfaceCRUDFormWidget $widget_obj, string $comment_str = '')
    {
        $this->setTitle($title);
        $this->setWidgetObj($widget_obj);
        $this->setCommentStr($comment_str);
    }

    /** @inheritdoc */
    public function html(InterfaceEntity $obj, CRUD $crud): string
    {
        $html = '';

        $required = false; // TODO

        $html .= '<div class="form-group ' . ($required ? 'required' : '') . '">';
        $html .= '<label class="col-sm-4 text-right control-label">' . $this->getTitle() . '</label>';

        $html .= '<div class="col-sm-8">';
        $html .= $this->getWidgetObj()->html($obj, $crud);

        if ($this->getCommentStr()) {
            $html .= '<span class="help-block">' . $this->getCommentStr() . '</span>';
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return InterfaceCRUDFormWidget
     */
    public function getWidgetObj(): InterfaceCRUDFormWidget
    {
        return $this->widget_obj;
    }

    /**
     * @param InterfaceCRUDFormWidget $widget_obj
     */
    public function setWidgetObj(InterfaceCRUDFormWidget $widget_obj): void
    {
        $this->widget_obj = $widget_obj;
    }

    /**
     * @return string
     */
    public function getCommentStr(): string
    {
        return $this->comment_str;
    }

    /**
     * @param string $comment_str
     */
    public function setCommentStr(string $comment_str): void
    {
        $this->comment_str = $comment_str;
    }
}
