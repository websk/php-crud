<?php

namespace WebSK\CRUD\Table\Widgets;

use Closure;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDCompiler;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;
use WebSK\Entity\InterfaceEntity;

/**
 * Class CRUDTableWidgetHtml
 * @package WebSK\CRUD
 */
class CRUDTableWidgetHtml implements InterfaceCRUDTableWidget
{
    /** @var string|Closure */
    protected $html;

    /**
     * CRUDTableWidgetHtml constructor.
     * @param string|Closure $html
     */
    public function __construct($html)
    {
        $this->setHtml($html);
    }

    /** @inheritdoc */
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string
    {
        return CRUDCompiler::fieldValueOrCallableResult($this->getHtml(), $entity_obj);
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
}
