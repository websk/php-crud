<?php

namespace WebSK\CRUD\Table;

/**
 * Interface InterfaceCRUDTableColumn
 * @package WebSK\CRUD
 */
interface InterfaceCRUDTableColumn
{
    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return InterfaceCRUDTableWidget
     */
    public function getWidgetObj(): InterfaceCRUDTableWidget;
}
