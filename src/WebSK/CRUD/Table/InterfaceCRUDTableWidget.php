<?php

namespace WebSK\CRUD\Table;

use WebSK\Entity\InterfaceEntity;
use WebSK\CRUD\CRUD;

/**
 * Interface InterfaceCRUDTableWidget
 * @package WebSK\CRUD
 */
interface InterfaceCRUDTableWidget
{
    /**
     * @param InterfaceEntity $obj
     * @param CRUD $crud
     * @return string
     */
    public function html($obj, CRUD $crud): string;
}
