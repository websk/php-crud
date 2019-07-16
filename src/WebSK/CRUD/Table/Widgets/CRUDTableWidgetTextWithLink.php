<?php

namespace WebSK\CRUD\Table\Widgets;

use Closure;
use OLOG\HTML;
use WebSK\CRUD\CRUDCompiler;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetTextWithLink
 * @package WebSK\CRUD
 */
class CRUDTableWidgetTextWithLink implements InterfaceCRUDTableWidget
{
    /** @var string|Closure */
    protected $text;

    /** @var string|Closure */
    protected $link;

    /** @var string */
    protected $classes_str;

    /** @var string */
    protected $target = '';

    /** @var string */
    protected $rel = '';

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $url = CRUDCompiler::fieldValueOrCallableResult($this->getLink(), $obj);

        $text = CRUDCompiler::fieldValueOrCallableResult($this->getText(), $obj);
        if (trim($text) == '') {
            $text = '#EMPTY#';
        }
        $text = Sanitize::sanitizeTagContent($text);

        $link_attrs_arr = [];
        $link_attrs_arr['href'] = $url;

        if ($this->getClassesStr() != '') {
            $link_attrs_arr['class'] = $this->getClassesStr();
        }

        if ($this->getTarget() != '') {
            $link_attrs_arr['target'] = $this->getTarget();
        }

        if ($this->getRel() != '') {
            $link_attrs_arr['rel'] = $this->getRel();
        }

        return HTML::tag('a', $link_attrs_arr, $text);
    }

    /**
     * CRUDTableWidgetTextWithLink constructor.
     * @param string|Closure $text
     * @param string|Closure $link
     * @param string $classes_str
     * @param string $target
     * @param string $rel
     */
    public function __construct(
        $text,
        $link,
        string $classes_str = '',
        string $target = '',
        string $rel = ''
    ) {
        $this->setText($text);
        $this->setLink($link);
        $this->setClassesStr($classes_str);
        $this->setTarget($target);
        $this->setRel($rel);
    }

    /**
     * @return string|Closure
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string|Closure $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @return Closure|string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param Closure|string $link
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

    /**
     * @return string
     */
    public function getRel(): string
    {
        return $this->rel;
    }

    /**
     * @param string $rel
     */
    public function setRel(string $rel): void
    {
        $this->rel = $rel;
    }
}
