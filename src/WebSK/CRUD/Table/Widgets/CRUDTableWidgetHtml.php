<?php

namespace WebSK\CRUD\Table\Widgets;

use WebSK\CRUD\CRUD;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetHtml
 * @package WebSK\CRUD
 */
class CRUDTableWidgetHtml implements InterfaceCRUDTableWidget
{
    /** @var string */
    protected $html;

    /**
     * CRUDTableWidgetHtml constructor.
     * @param string $html
     */
    public function __construct(string $html)
    {
        $this->setHtml($html);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        return $crud->compile($this->getHtml(), ['this' => $obj]);
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
}
