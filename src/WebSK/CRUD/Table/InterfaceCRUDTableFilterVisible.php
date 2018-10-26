<?php

namespace WebSK\CRUD\Table;

use Slim\Http\Request;

/**
 * Interface InterfaceCRUDTableFilterVisible
 * @package WebSK\CRUD
 */
interface InterfaceCRUDTableFilterVisible extends InterfaceCRUDTableFilter
{
    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @param Request $request
     * @return string
     */
    public function getHtml(Request $request): string;
}
