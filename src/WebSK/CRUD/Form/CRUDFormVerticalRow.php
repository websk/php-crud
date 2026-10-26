<?php

namespace WebSK\CRUD\Form;

use WebSK\CRUD\CRUD;
use WebSK\Entity\InterfaceEntity;

/**
 * Class CRUDFormVerticalRow
 * @package WebSK\CRUD
 */
class CRUDFormVerticalRow implements InterfaceCRUDFormRow
{
    protected string $title;

    protected InterfaceCRUDFormWidget $widget_obj;

    protected string $comment_str;

    /**
     * CRUDFormVerticalRow constructor.
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
        $label_id = uniqid('CRUDFormRowLabel_');
        $widget_html = CRUDFormAccessibility::associateFirstControl(
            $this->getWidgetObj()->html($obj, $crud),
            $label_id
        );

        $html .= '<div class="row mb-3">';
        $html .= '<div id="' . $label_id . '" class="col-sm-12 col-form-label">' . $this->getTitle() . '</div>';
        $html .= '<div class="col-sm-12">';

        $html .= $widget_html;

        if ($this->getCommentStr()) {
            $html .= '<div class="form-text">' . $this->getCommentStr() . '</div>';
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
