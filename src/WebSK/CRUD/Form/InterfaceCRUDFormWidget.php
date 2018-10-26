<?php

namespace WebSK\CRUD\Form;

use WebSK\CRUD\CRUD;

/**
 * Interface InterfaceCRUDFormWidget
 * @package WebSK\CRUD
 */
interface InterfaceCRUDFormWidget
{
    /**
     * @param object $obj
     * @param CRUD $crud
     * @return string
     */
    public function html($obj, CRUD $crud): string;
}
