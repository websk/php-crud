<?php

namespace WebSK\CRUD\Table\Widgets;

use Closure;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDCompiler;
use WebSK\Entity\InterfaceEntity;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetText
 * @package WebSK\CRUD
 */
class CRUDTableWidgetText implements InterfaceCRUDTableWidget
{
    /** @var string|Closure */
    protected $text;

    /**
     * CRUDTableWidgetText constructor.
     * @param string|Closure $text
     */
    public function __construct($text)
    {
        $this->setText($text);
    }

    /** @inheritdoc */
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string
    {
        $html = CRUDCompiler::fieldValueOrCallableResult($this->getText(), $entity_obj);

        return Sanitize::sanitizeTagContent($html);
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
}
