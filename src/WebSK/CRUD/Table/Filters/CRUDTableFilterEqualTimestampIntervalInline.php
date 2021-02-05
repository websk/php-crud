<?php

namespace WebSK\CRUD\Table\Filters;

use Slim\Http\Request;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterVisible;

/**
 * Class CRUDTableFilterEqualTimestampIntervalInline
 * @package WebSK\CRUD\Table\Filters
 */
class CRUDTableFilterEqualTimestampIntervalInline extends CRUDTableFilterEqualIntervalInline
    implements InterfaceCRUDTableFilterVisible
{
    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(Request $request): array
    {
        $where = '';
        $placeholder_values_arr = [];

        // для этого виджета галка включения не выводится: если в поле пустая строка - он игрорируется
        $value_start_dt = $request->getParam($this->getFilterStartUniqId());
        $value_start = $value_start_dt ? (new \DateTime($value_start_dt))->getTimestamp() : null;

        $value_end_dt = $request->getParam($this->getFilterEndUniqId());
        $value_end = $value_end_dt ? (new \DateTime($value_end_dt))->getTimestamp() : null;

        $column_name = $this->getFieldName();
        $column_name = preg_replace("/[^a-zA-Z0-9_]+/", "", $column_name);

        if ($value_start) {
            $where .= ' ' . $column_name . ' >= ? ';
            $placeholder_values_arr[] = $value_start;
        }

        if ($value_end) {
            if ($value_start != '') {
                $where .= ' AND ';
            }
            $where .= ' ' . $column_name . ' <= ? ';
            $placeholder_values_arr[] = $value_end;
        }

        return [$where, $placeholder_values_arr];
    }
}