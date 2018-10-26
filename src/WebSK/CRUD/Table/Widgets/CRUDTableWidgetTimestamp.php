<?php

namespace WebSK\CRUD\Table\Widgets;

use WebSK\CRUD\CRUD;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetTimestamp
 * @package WebSK\CRUD
 */
class CRUDTableWidgetTimestamp implements InterfaceCRUDTableWidget
{
    /** @var string */
    protected $timestamp;
    /** @var string */
    protected $format;

    /**
     * CRUDTableWidgetTimestamp constructor.
     * @param string $timestamp
     * @param string $format
     */
    public function __construct(string $timestamp, string $format = "Y-m-d H:i:s")
    {
        $this->setTimestamp($timestamp);
        $this->setFormat($format);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $timestamp = $crud->compile($this->getTimestamp(), ['this' => $obj]);
        if(is_null($timestamp)) {
            return '';
        }
        $date = date($this->getFormat(), $timestamp);
        return Sanitize::sanitizeTagContent($date);
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp(string $timestamp): void
    {
        $this->timestamp = $timestamp;
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
