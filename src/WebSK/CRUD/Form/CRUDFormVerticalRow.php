<?php

namespace WebSK\CRUD\Form;

use WebSK\CRUD\CRUD;

/**
 * Class CRUDFormVerticalRow
 * @package WebSK\CRUD
 */
class CRUDFormVerticalRow implements InterfaceCRUDFormRow
{
    /** @var string */
    protected $title;
    /** @var InterfaceCRUDFormWidget */
    protected $widget_obj;
    /** @var string */
    protected $comment_str;

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
    public function html($obj, CRUD $crud): string
    {
        $html = '';

        $html .= '<div class="form-group">';
        $html .= '<div class="col-sm-12"><label>' . $this->getTitle() . '</label></div>';
        $html .= '<div class="col-sm-12">';

        $html .= $this->getWidgetObj()->html($obj, $crud);

        if ($this->getCommentStr()) {
            $html .= '<div class="col-sm-12">';
            $html .= '<span class="help-block">' . $this->getCommentStr() . '</span>';
            $html .= '</div>';
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
