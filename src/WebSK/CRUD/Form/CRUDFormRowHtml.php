<?php

namespace WebSK\CRUD\Form;

use WebSK\CRUD\CRUD;
use WebSK\Entity\InterfaceEntity;

/**
 * Class CRUDFormRowHtml
 * @package WebSK\CRUD
 */
class CRUDFormRowHtml implements InterfaceCRUDFormRow
{
    protected string $html;

    /**
     * CRUDFormRowHtml constructor.
     * @param string $html
     */
    public function __construct(string $html)
    {
        $this->setHtml($html);
    }

    /** @inheritdoc */
    public function html(InterfaceEntity $obj, CRUD $crud): string
    {
        return $this->getHtml();
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
