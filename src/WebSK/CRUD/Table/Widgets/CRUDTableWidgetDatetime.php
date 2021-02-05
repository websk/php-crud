<?php

namespace WebSK\CRUD\Table\Widgets;

use Closure;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDCompiler;
use WebSK\Entity\InterfaceEntity;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetDatetime
 * @package WebSK\CRUD
 */
class CRUDTableWidgetDatetime implements InterfaceCRUDTableWidget
{
    /** @var string|Closure */
    protected $datetime;

    protected string $format = "d.m.Y H:i:s";

    /**
     * CRUDTableWidgetDatetime constructor.
     * @param string|Closure $datetime
     * @param string $format
     */
    public function __construct($datetime, $format = "d.m.Y H:i:s")
    {
        $this->setDatetime($datetime);
        $this->setFormat($format);
    }

    /** @inheritdoc */
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string
    {
        $datetime = CRUDCompiler::fieldValueOrCallableResult($this->getDatetime(), $entity_obj);
        $date_obj = new \DateTime($datetime);
        $date = $date_obj->format($this->getFormat());

        return Sanitize::sanitizeTagContent($date);
    }

    /**
     * @return string|Closure
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param string|Closure $datetime
     */
    public function setDatetime($datetime): void
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
