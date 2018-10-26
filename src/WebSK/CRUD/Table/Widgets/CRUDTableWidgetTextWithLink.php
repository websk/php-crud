<?php

namespace WebSK\CRUD\Table\Widgets;

use OLOG\HTML;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetTextWithLink
 * @package WebSK\CRUD
 */
class CRUDTableWidgetTextWithLink implements InterfaceCRUDTableWidget
{
    /** @var string */
    protected $text;
    /** @var string */
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
        $url = $crud->compile($this->getLink(), ['this' => $obj]);

        $text = $crud->compile($this->getText(), ['this' => $obj]);
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
     * @param string $text
     * @param string $link
     * @param string $classes_str
     * @param string $target
     * @param string $rel
     */
    public function __construct(string $text, string $link, string $classes_str = '', string $target = '', string $rel = '')
    {
        $this->setText($text);
        $this->setLink($link);
        $this->setClassesStr($classes_str);
        $this->setTarget($target);
        $this->setRel($rel);
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
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
