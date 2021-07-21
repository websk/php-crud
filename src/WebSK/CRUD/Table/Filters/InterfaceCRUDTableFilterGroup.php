<?php

namespace WebSK\CRUD\Table\Filters;

use Psr\Http\Message\ServerRequestInterface;
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
     * @param ServerRequestInterface $request
     * @return string
     */
    public function getHtml(ServerRequestInterface $request): string;

    /**
     * @return InterfaceCRUDTableFilter[]
     */
    public function getFiltersArr(): array;
}