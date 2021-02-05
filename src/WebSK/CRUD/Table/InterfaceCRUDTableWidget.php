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
     * @param InterfaceEntity $entity_obj
     * @param CRUD $crud
     * @return string
     */
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string;
}
