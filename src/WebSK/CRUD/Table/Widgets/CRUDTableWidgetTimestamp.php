<?php

namespace WebSK\CRUD\Table\Widgets;

use Closure;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDCompiler;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;
use WebSK\Entity\InterfaceEntity;
use WebSK\Utils\DateTime;
use WebSK\Utils\Sanitize;

/**
 * Class CRUDTableWidgetTimestamp
 * @package WebSK\CRUD
 */
class CRUDTableWidgetTimestamp implements InterfaceCRUDTableWidget
{
    /** @var string|Closure */
    protected $timestamp;

    protected string $format;

    /** @var \DateTimeZone|null */
    protected $timezone;

    /**
     * CRUDTableWidgetTimestamp constructor.
     * @param string $timestamp
     * @param string $format
     * @param \DateTimeZone|null $timezone
     */
    public function __construct(string $timestamp, string $format = "Y-m-d H:i:s", $timezone = null)
    {
        $this->setTimestamp($timestamp);
        $this->setFormat($format);
        $this->setTimezone($timezone);
    }

    /** @inheritdoc */
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string
    {
        $timestamp = CRUDCompiler::fieldValueOrCallableResult($this->getTimestamp(), $entity_obj);
        if (is_null($timestamp)) {
            return '';
        }

        $datetime_obj = DateTime::createFromTimestamp($timestamp, $this->getTimezone());
        $date = $datetime_obj->format($this->getFormat());

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


    /**
     * @return \DateTimeZone|null
     */
    public function getTimezone(): ?\DateTimeZone
    {
        return $this->timezone;
    }

    /**
     * @param \DateTimeZone|null $timezone
     */
    public function setTimezone(?\DateTimeZone $timezone): void
    {
        $this->timezone = $timezone;
    }
}
