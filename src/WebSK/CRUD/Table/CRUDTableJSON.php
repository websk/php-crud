<?php

namespace WebSK\CRUD\Table;

use OLOG\Assert;
use Slim\Http\Request;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;

/**
 * Class CRUDTableJSON
 * @package WebSK\CRUD\Table
 */
class CRUDTableJSON
{
    const PAGE_SIZE = 10000;

    const JSON_FIELD_VALUE = 'value';
    const JSON_FIELD_TEXT = 'text';

    protected CRUD $crud;

    protected string $entity_class_name;

    protected array $column_obj_arr;

    protected array $filters_arr;

    protected string $order_by = '';

    /**
     * CRUDTable constructor.
     * @param CRUD $crud
     * @param string $entity_class_name
     * @param InterfaceCRUDTableColumn[] $column_obj_arr
     * @param InterfaceCRUDTableFilter[] $filters_arr
     * @param string $order_by
     */
    public function __construct(
        CRUD $crud,
        string $entity_class_name,
        array $column_obj_arr,
        array $filters_arr = [],
        string $order_by = ''
    ) {
        $this->crud = $crud;
        $this->entity_class_name = $entity_class_name;
        $this->column_obj_arr = $column_obj_arr;
        $this->filters_arr = $filters_arr;
        $this->order_by = $order_by;
    }

    /**
     * @param Request $request
     */
    public function json(Request $request)
    {
        $data_arr = [];

        $objs_ids_arr = $this->crud->getObjIdsArrForClassName(
            $request,
            $this->entity_class_name,
            $this->filters_arr,
            $this->order_by,
            self::PAGE_SIZE
        );

        foreach ($objs_ids_arr as $obj_id) {
            $entity_obj = $this->crud->createAndLoadObject($this->entity_class_name, $obj_id);
            $data_item = [];
            foreach ($this->column_obj_arr as $column_obj) {
                Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);

                $widget_obj = $column_obj->getWidgetObj();
                Assert::assert($widget_obj);
                Assert::assert($widget_obj instanceof InterfaceCRUDTableWidget);

                $field_name = $column_obj->getFieldName();
                if (!$field_name) {
                    continue;
                }

                if (CRUDFieldsAccess::objectHasProperty($entity_obj, $field_name)) {
                    $field_value = CRUDFieldsAccess::getObjectFieldValue($entity_obj, $field_name);
                    $data_item[$field_name] = $field_value;
                }
            }
            $data_arr[] = $data_item;
        }

        return json_encode($data_arr);
    }
}
