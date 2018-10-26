<?php

namespace WebSK\CRUD\Table;

/**
 * Class CRUDTableColumn
 * @package WebSK\CRUD
 */
class CRUDTableColumn implements InterfaceCRUDTableColumn
{
    /** @var string */
    protected $title;
    /** @var InterfaceCRUDTableWidget */
    protected $widget_obj;

    /**
     * CRUDTableColumn constructor.
     * @param string $title
     * @param InterfaceCRUDTableWidget $widget_obj
     */
    public function __construct(string $title, InterfaceCRUDTableWidget $widget_obj)
    {
        $this->setTitle($title);
        $this->setWidgetObj($widget_obj);
    }

    /** @inheritdoc */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /** @inheritdoc */
    public function getWidgetObj(): InterfaceCRUDTableWidget
    {
        return $this->widget_obj;
    }

    /**
     * @param InterfaceCRUDTableWidget $widget_obj
     */
    public function setWidgetObj(InterfaceCRUDTableWidget $widget_obj): void
    {
        $this->widget_obj = $widget_obj;
    }
}
