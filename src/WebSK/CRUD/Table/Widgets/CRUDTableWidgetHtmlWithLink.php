<?php

namespace WebSK\CRUD\Table\Widgets;

use Closure;
use WebSK\CRUD\CRUD;
use OLOG\HTML;
use WebSK\CRUD\CRUDCompiler;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;
use WebSK\Entity\InterfaceEntity;

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

    protected string $classes_str = '';

    protected string $target = '';

    /**
     * CRUDTableWidgetHtmlWithLink constructor.
     * @param string|Closure $html
     * @param string|Closure $link
     * @param string $classes_str
     */
    public function __construct($html, $link, string $classes_str = '', string $target = '')
    {
        $this->setHtml($html);
        $this->setLink($link);
        $this->setClassesStr($classes_str);
        $this->setTarget($target);
    }

    /** @inheritdoc */
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string
    {
        $url = CRUDCompiler::fieldValueOrCallableResult($this->getLink(), $entity_obj);

        $html = CRUDCompiler::fieldValueOrCallableResult($this->getHtml(), $entity_obj);
        $html = trim($html);

        if ($html == '') {
            $html = '#EMPTY#';
        }
        $link_attrs_arr = [];
        $link_attrs_arr['href'] = $url;

        if ($this->getClassesStr() != '') {
            $link_attrs_arr['class'] = $this->getClassesStr();
        }

        if ($this->getTarget() != '') {
            $link_attrs_arr['target'] = $this->getTarget();
        }

        return HTML::tag('a', $link_attrs_arr, $html);
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

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget(string $target): void
    {
        $this->target = $target;
    }
}
