<?php

namespace WebSK\CRUD\Form;

use WebSK\CRUD\CRUD;

/**
 * Interface InterfaceCRUDFormRow
 * @package WebSK\CRUD\Form
 */
interface InterfaceCRUDFormRow
{
    /**
     * @param object $obj
     * @param CRUD $crud
     * @return string
     */
    public function html($obj, CRUD $crud): string;
}
