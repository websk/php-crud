<?php

namespace WebSK\CRUD\Form;

use WebSK\CRUD\CRUD;
use WebSK\Entity\InterfaceEntity;

/**
 * Interface InterfaceCRUDFormWidget
 * @package WebSK\CRUD
 */
interface InterfaceCRUDFormWidget
{
    /**
     * @param InterfaceEntity $entity_obj
     * @param CRUD $crud
     * @return string
     */
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string;
}
