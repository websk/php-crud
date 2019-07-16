<?php

namespace WebSK\CRUD\Table\Widgets;

use Closure;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDCompiler;
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
    public function html($obj, CRUD $crud): string
    {
        $html = CRUDCompiler::fieldValueOrCallableResult($this->getText(), $obj);

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
