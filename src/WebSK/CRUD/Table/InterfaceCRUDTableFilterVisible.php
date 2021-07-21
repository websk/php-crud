<?php

namespace WebSK\CRUD\Table;

use Psr\Http\Message\ServerRequestInterface;

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
     * @param ServerRequestInterface $request
     * @return string
     */
    public function getHtml(ServerRequestInterface $request): string;
}
