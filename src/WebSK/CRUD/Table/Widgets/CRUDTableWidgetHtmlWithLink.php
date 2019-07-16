<?php

namespace WebSK\CRUD\Table\Widgets;

use Closure;
use WebSK\CRUD\CRUD;
use OLOG\HTML;
use WebSK\CRUD\CRUDCompiler;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetHtmlWithLink
 * @package WebSK\CRUD
 */
class CRUDTableWidgetHtmlWithLink implements InterfaceCRUDTableWidget
{
    /** @var string|Closure */
    protected $html;

    /** @var string|Closure */
    protected $link;

    /** @var string */
    protected $classes_str;

    /**
     * CRUDTableWidgetHtmlWithLink constructor.
     * @param string|Closure $html
     * @param string|Closure $link
     * @param string $classes_str
     */
    public function __construct($html, $link, string $classes_str = '')
    {
        $this->setHtml($html);
        $this->setLink($link);
        $this->setClassesStr($classes_str);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $url = CRUDCompiler::fieldValueOrCallableResult($this->getLink(), $obj);

        $html = CRUDCompiler::fieldValueOrCallableResult($this->getHtml(), $obj);
        $html = trim($html);

        if ($html == '') {
            $html = '#EMPTY#';
        }

        return HTML::tag('a', ['href' => $url, 'class' => $this->getClassesStr()], $html);
    }

    /**
     * @return string|Closure
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param string|Closure $html
     */
    public function setHtml($html): void
    {
        $this->html = $html;
    }

    /**
     * @return string|Closure
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string|Closure $link
     */
    public function setLink($link): void
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
