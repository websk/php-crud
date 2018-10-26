<?php

namespace WebSK\CRUD\Table;

use Slim\Http\Request;

/**
 * Interface InterfaceCRUDTableFilter
 * @package WebSK\CRUD
 */
interface InterfaceCRUDTableFilter
{
    /**
     * Возвращает пару из sql-условия и массива значений плейсхолдеров.
     * Массив значений может быть пустой если плейсхолдеры не нужны.
     * @param Request $request
     * @return array
     */
    public function sqlConditionAndPlaceholderValue(Request $request): array;
}
