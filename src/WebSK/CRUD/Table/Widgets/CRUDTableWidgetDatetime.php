<?php

namespace WebSK\CRUD\Table\Widgets;

use WebSK\CRUD\CRUD;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetDatetime
 * @package WebSK\CRUD
 */
class CRUDTableWidgetDatetime implements InterfaceCRUDTableWidget
{
    /** @var string */
    protected $datetime;
    /** @var string */
    protected $format;

    /**
     * CRUDTableWidgetDatetime constructor.
     * @param string $datetime
     * @param string $format
     */
    public function __construct(string $datetime, $format = "d.m.Y H:i:s")
    {
        $this->setDatetime($datetime);
        $this->setFormat($format);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $datetime = $crud->compile($this->getDatetime(), ['this' => $obj]);
        $date_obj = new \DateTime($datetime);
        $date = $date_obj->format($this->getFormat());
        return Sanitize::sanitizeTagContent($date);
    }

    /**
     * @return string
     */
    public function getDatetime(): string
    {
        return $this->datetime;
    }

    /**
     * @param string $datetime
     */
    public function setDatetime(string $datetime): void
    {
        $this->datetime = $datetime;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }
}
