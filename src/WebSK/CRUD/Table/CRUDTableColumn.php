<?php

namespace WebSK\CRUD\Table;

/**
 * Class CRUDTableColumn
 * @package WebSK\CRUD
 */
class CRUDTableColumn implements InterfaceCRUDTableColumn
{
    protected string $title;

    protected InterfaceCRUDTableWidget $widget_obj;

    protected ?string $field_name = null;

    /**
     * CRUDTableColumn constructor.
     * @param string $title
     * @param InterfaceCRUDTableWidget $widget_obj
     * @param string|null $field_name
     */
    public function __construct(string $title, InterfaceCRUDTableWidget $widget_obj, ?string $field_name = null)
    {
        $this->setTitle($title);
        $this->setWidgetObj($widget_obj);
        $this->setFieldName($field_name);
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

    /** @inheritdoc */
    public function getFieldName(): ?string
    {
        return $this->field_name;
    }

    /**
     * @param string|null $field_name
     */
    public function setFieldName(?string $field_name): void
    {
        $this->field_name = $field_name;
    }
}
