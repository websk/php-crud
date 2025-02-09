<?php

namespace WebSK\CRUD\Form\Widgets;

use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;
use WebSK\Entity\InterfaceEntity;

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

    /**
     * @param InterfaceEntity $entity_obj
     * @param CRUD $crud
     * @return string
     */
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string
    {
        return $this->getHtml();
    }
}
