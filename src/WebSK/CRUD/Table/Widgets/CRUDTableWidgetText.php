<?php

namespace WebSK\CRUD\Table\Widgets;

use WebSK\CRUD\CRUD;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetText
 * @package WebSK\CRUD
 */
class CRUDTableWidgetText implements InterfaceCRUDTableWidget
{
    /** @var string */
    protected $text;

    public function __construct(string $text)
    {
        $this->setText($text);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $html = $crud->compile($this->getText(), ['this' => $obj]);
        return Sanitize::sanitizeTagContent($html);
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
}
