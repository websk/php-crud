<?php

namespace WebSK\CRUD\Form\Widgets;

use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;

/**
 * Class CRUDFormWidgetHtml
 * @package WebSK\CRUD
 */
class CRUDFormWidgetHtml implements InterfaceCRUDFormWidget
{
    protected string $html = '';

    /**
     * CRUDFormWidgetHtml constructor.
     * @param $html
     */
    public function __construct(string $html)
    {
        $this->setHtml($html);
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

    /** @inheritdoc */
    public function html($entity_obj, CRUD $crud): string
    {
        return $this->getHtml();
    }
}
