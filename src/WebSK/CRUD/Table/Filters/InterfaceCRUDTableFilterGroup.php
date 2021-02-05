<?php

namespace WebSK\CRUD\Table\Filters;

use Slim\Http\Request;
use WebSK\CRUD\Table\InterfaceCRUDTableFilter;

/**
 * Interface InterfaceCRUDTableFilterGroup
 * @package WebSK\CRUD\Table\Filters
 */
interface InterfaceCRUDTableFilterGroup
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

    /**
     * @return InterfaceCRUDTableFilter[]
     */
    public function getFiltersArr(): array;
}