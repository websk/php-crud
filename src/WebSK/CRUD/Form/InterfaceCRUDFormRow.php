<?php

namespace WebSK\CRUD\Form;

use WebSK\CRUD\CRUD;
use WebSK\Entity\InterfaceEntity;

/**
 * Interface InterfaceCRUDFormRow
 * @package WebSK\CRUD\Form
 */
interface InterfaceCRUDFormRow
{
    /**
     * @param InterfaceEntity $obj
     * @param CRUD $crud
     * @return string
     */
    public function html(InterfaceEntity $obj, CRUD $crud): string;
}
