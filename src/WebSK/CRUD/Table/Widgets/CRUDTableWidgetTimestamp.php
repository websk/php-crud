<?php

namespace WebSK\CRUD\Table\Widgets;

use Closure;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDCompiler;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetTimestamp
 * @package WebSK\CRUD
 */
class CRUDTableWidgetTimestamp implements InterfaceCRUDTableWidget
{
    /** @var string|Closure */
    protected $timestamp;

    /** @var string */
    protected $format;

    /**
     * CRUDTableWidgetTimestamp constructor.
     * @param string|Closure $timestamp
     * @param string $format
     */
    public function __construct($timestamp, string $format = "Y-m-d H:i:s")
    {
        $this->setTimestamp($timestamp);
        $this->setFormat($format);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $timestamp = CRUDCompiler::fieldValueOrCallableResult($this->getTimestamp(), $obj);
        if (is_null($timestamp)) {
            return '';
        }
        $date = date($this->getFormat(), $timestamp);
        return Sanitize::sanitizeTagContent($date);
    }

    /**
     * @return string|Closure
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string|Closure $timestamp
     */
    public function setTimestamp($timestamp): void
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
