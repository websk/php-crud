<?php

namespace WebSK\CRUD\Table\Widgets;

use WebSK\CRUD\CRUD;
use OLOG\HTML;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetHtmlWithLink
 * @package WebSK\CRUD
 */
class CRUDTableWidgetHtmlWithLink implements InterfaceCRUDTableWidget
{
    /** @var string */
    protected $html;
    /** @var string */
    protected $link;
    /** @var string */
    protected $classes_str;

    /**
     * CRUDTableWidgetHtmlWithLink constructor.
     * @param string $html
     * @param string $link
     * @param string $classes_str
     */
    public function __construct(string $html, string $link, string $classes_str = '')
    {
        $this->setHtml($html);
        $this->setLink($link);
        $this->setClassesStr($classes_str);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $url = $crud->compile($this->getLink(), ['this' => $obj]);

        $html = $crud->compile($this->getHtml(), ['this' => $obj]);
        $html = trim($html);

        if ($html == '') {
            $html = '#EMPTY#';
        }

        return HTML::tag('a', ['href' => $url, 'class' => $this->getClassesStr()], $html);
    }

    /**
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * @param string $html
     */
    public function setHtml(string $html): void
    {
        $this->html = $html;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getClassesStr(): string
    {
        return $this->classes_str;
    }

    /**
     * @param string $classes_str
     */
    public function setClassesStr(string $classes_str): void
    {
        $this->classes_str = $classes_str;
    }
}
