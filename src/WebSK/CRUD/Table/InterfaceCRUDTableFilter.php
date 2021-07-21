<?php

namespace WebSK\CRUD\Table;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface InterfaceCRUDTableFilter
 * @package WebSK\CRUD
 */
interface InterfaceCRUDTableFilter
{
    /**
     * Возвращает пару из sql-условия и массива значений плейсхолдеров.
     * Массив значений может быть пустой если плейсхолдеры не нужны.
     * @param ServerRequestInterface $request
     * @return array
     */
    public function sqlConditionAndPlaceholderValue(ServerRequestInterface $request): array;
}
