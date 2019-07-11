<?php

namespace WebSK\CRUD\Table;

use WebSK\Utils\Assert;
use OLOG\CheckClassInterfaces;
use OLOG\HTML;
use OLOG\MagnificPopup;
use OLOG\Operations;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Entity\InterfaceWeight;
use WebSK\Utils\HTTP;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDForm;
use WebSK\CRUD\NullablePostFields;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetWeight;
use WebSK\CRUD\Pager;

/**
 * Class CRUDTable
 * @package WebSK\CRUD
 */
class CRUDTable
{
    const KEY_LIST_COLUMNS = 'LIST_COLUMNS';

    const OPERATION_ADD_ENTITY = 'OPERATION_ADD_ENTITY';
    const OPERATION_DELETE_ENTITY = 'OPERATION_DELETE_ENTITY';
    const OPERATION_SWAP_ENTITY_WEIGHT = 'OPERATION_SWAP_ENTITY_WEIGHT';
    const OPERATION_UPDATE_ENTITY_FIELD = 'OPERATION_UPDATE_ENTITY_FIELD';

    const FILTERS_POSITION_LEFT = 'FILTERS_POSITION_LEFT';
    const FILTERS_POSITION_RIGHT = 'FILTERS_POSITION_RIGHT';
    const FILTERS_POSITION_TOP = 'FILTERS_POSITION_TOP';
    const FILTERS_POSITION_NONE = 'FILTERS_POSITION_NONE';
    const FILTERS_POSITION_INLINE = 'FILTERS_POSITION_INLINE';

    const FIELD_CRUDTABLE_ID = 'crudtable_id';
    const FIELD_FIELD_NAME = 'field_name';
    const FIELD_FIELD_VALUE = 'field_value';
    const FIELD_ENTITY_ID = 'entity_id';

    const FIELD_NAME_CRUD_TABLE_ID = '_FIELD_NAME_CRUD_TABLE_ID';
    const FIELD_NAME_ENTITY_CLASS_NAME = '_FIELD_NAME_ENTITY_CLASS_NAME';

    /** @var CRUD */
    protected $crud;

    /** @var string */
    protected $entity_class_name;

    /** @var CRUDForm */
    protected $create_form_obj;

    /** @var InterfaceCRUDTableColumn[] */
    protected $column_obj_arr;

    /** @var InterfaceCRUDTableFilter[] */
    protected $filters_arr;

    /** @var string */
    protected $order_by = '';

    /** @var string */
    protected $table_id = '';

    /** @var string */
    protected $filters_position = self::FILTERS_POSITION_NONE;

    /** @var bool */
    protected $display_total_rows_count = false;

    /** @var int */
    protected $page_size;

    /**
     * CRUDTable constructor.
     * @param CRUD $crud
     * @param string $entity_class_name
     * @param null|CRUDForm $create_form_obj
     * @param InterfaceCRUDTableColumn[] $column_obj_arr
     * @param InterfaceCRUDTableFilter[] $filters_arr
     * @param string $order_by
     * @param string $table_id
     * @param string $filters_position
     * @param bool $display_total_rows_count
     * @param int $page_size
     */
    public function __construct(
        CRUD $crud,
        string $entity_class_name,
        CRUDForm $create_form_obj = null,
        array $column_obj_arr = [],
        array $filters_arr = [],
        string $order_by = '',
        string $table_id = '',
        string $filters_position = self::FILTERS_POSITION_NONE,
        bool $display_total_rows_count = false,
        int $page_size = 30
    ) {
        $this->crud = $crud;
        $this->entity_class_name = $entity_class_name;
        $this->create_form_obj = $create_form_obj;
        $this->column_obj_arr = $column_obj_arr;
        $this->filters_arr = $filters_arr;
        $this->order_by = $order_by;
        $this->table_id = $table_id;
        $this->filters_position = $filters_position;
        $this->display_total_rows_count = $display_total_rows_count;
        $this->page_size = $page_size;
    }

    /**
     * @param Request $request
     * @return string
     */
    public function html(Request $request): string
    {
        //
        // вывод таблицы
        //

        $table_container_element_id = uniqid('tableContainer_');
        if ($this->table_id) {
            $table_container_element_id = $this->table_id;
        }

        // оборачиваем в отдельный div для выдачи только таблицы аяксом -
        // иначе корневой элемент документа не будет доступен в jquery селекторах
        $html = HTML::div($table_container_element_id, '', function () use (
            $request
        ) {

            echo '<div class="row">';

            if ($this->filters_position == self::FILTERS_POSITION_LEFT) {
                echo '<div class="col-sm-4">';
                echo self::filtersHtml($request, $this->filters_arr);
                echo '</div>';
            }

            $col_sm_class = '12';
            if (($this->filters_position == self::FILTERS_POSITION_LEFT) ||
                ($this->filters_position == self::FILTERS_POSITION_RIGHT)) {
                $col_sm_class = '8';
            }
            echo '<div class="col-sm-' . $col_sm_class . '">';

            if ($this->filters_position != self::FILTERS_POSITION_INLINE) {
                echo self::toolbarHtml(
                    $this->table_id,
                    $this->create_form_obj ? $this->create_form_obj->html() : '',
                    $this->filters_arr
                );
            }

            if ($this->filters_position == self::FILTERS_POSITION_TOP) {
                echo self::filtersHtml($request, $this->filters_arr);
            }

            if ($this->filters_position == self::FILTERS_POSITION_INLINE) {
                echo self::filtersAndCreateButtonHtmlInline(
                    $request,
                    $this->table_id,
                    $this->filters_arr,
                    $this->create_form_obj ? $this->create_form_obj->html() : ''
                );
            }

            echo '<table class="table table-hover">';

            $has_nonempty_th = false;
            foreach ($this->column_obj_arr as $column_obj) {
                Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);
                if ($column_obj->getTitle() != '') {
                    $has_nonempty_th = true;
                }
            }

            if ($has_nonempty_th) {
                echo '<thead><tr>';
                foreach ($this->column_obj_arr as $column_obj) {
                    Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);
                    echo '<th>' . $column_obj->getTitle() . '</th>';
                }
                echo '</tr></thead>';
            }

            echo '<tbody>';

            $page_size = Pager::getPageSize($request, $this->table_id, $this->page_size);
            $offset = Pager::getPageOffset($request, $this->table_id);
            $total_rows_count = 0;

            $objs_ids_arr = $this->crud->getObjIdsArrForClassName(
                $request,
                $this->entity_class_name,
                $this->filters_arr,
                $this->order_by,
                $page_size,
                $offset,
                $this->display_total_rows_count,
                $total_rows_count
            );

            foreach ($objs_ids_arr as $obj_id) {
                $obj_obj = $this->crud->createAndLoadObject($this->entity_class_name, $obj_id);
                echo '<tr>';
                foreach ($this->column_obj_arr as $column_obj) {
                    Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);
                    $widget_obj = $column_obj->getWidgetObj();
                    Assert::assert($widget_obj);

                    $col_width_attr = '';

                    if ($widget_obj instanceof CRUDTableWidgetDelete) {
                        $col_width_attr = ' width="1px" ';
                    }

                    if ($widget_obj instanceof CRUDTableWidgetWeight) {
                        $col_width_attr = ' width="1px" ';
                    }

                    echo '<td ' . $col_width_attr . ' style="word-break: break-all;">';
                    echo $widget_obj->html($obj_obj, $this->crud);
                    echo '</td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';

            echo '</table>';

            echo Pager::renderPager(
                $request,
                $this->table_id,
                count($objs_ids_arr),
                $this->display_total_rows_count,
                $total_rows_count,
                $this->page_size
            );

            echo '</div>';

            if ($this->filters_position == self::FILTERS_POSITION_RIGHT) {
                echo '<div class="col-sm-4">';
                echo self::filtersHtml($request, $this->filters_arr);
                echo '</div>';
            }

            echo '</div>';
        });

        // Загрузка скриптов
        $html .= CRUDTableScript::getHtml($table_container_element_id, $request->getUri()->getPath());

        return $html;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return null|Response
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function processRequest(Request $request, Response $response): ?Response
    {
        if ($this->create_form_obj instanceof CRUDForm) {
            $crud_form_response = $this->create_form_obj->processRequest($request, $response);
            if ($crud_form_response instanceof Response) {
                return $crud_form_response;
            }
        }

        if ($request->getMethod() != HTTP::METHOD_POST) {
            return null;
        }

        $operation_code = $request->getParsedBodyParam(Operations::FIELD_NAME_OPERATION_CODE);
        switch ($operation_code) {
            case self::OPERATION_DELETE_ENTITY:
                return $this->deleteEntityOperation($request, $response);
            case self::OPERATION_SWAP_ENTITY_WEIGHT:
                return $this->swapEntityWeightOperation($request, $response);
            case self::OPERATION_UPDATE_ENTITY_FIELD:
                return $this->updateEntityFieldOperation($request, $response);
        }

        return null;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function deleteEntityOperation(Request $request, Response $response): Response
    {
        // @TODO: do not pass DB table name in form - pass crud table id instead, get entity class name from crud table
        // @TODO: also check entity owner
        $entity_class_name = $request->getParsedBodyParam(CRUDTableWidgetDelete::FIELD_CLASS_NAME);
        Assert::assert($entity_class_name);

        $entity_id = $request->getParsedBodyParam(CRUDTableWidgetDelete::FIELD_OBJECT_ID);
        Assert::assert($entity_id);

        $this->crud->deleteObject($entity_class_name, $entity_id);

        $redirect_url = $request->getParsedBodyParam(CRUDTableWidgetDelete::FIELD_REDIRECT_AFTER_DELETE_URL, '');
        if ($redirect_url != '') {
            return $response->withRedirect($redirect_url);
        }

        return $response->withRedirect($request->getUri());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function swapEntityWeightOperation(Request $request, Response $response): Response
    {
        // @TODO: do not pass DB table name in form - pass crud table id instead, get entity class name from crud table
        // @TODO: also check entity owner
        $entity_class_name = $request->getParsedBodyParam(CRUDTableWidgetDelete::FIELD_CLASS_NAME);
        Assert::assert($entity_class_name);

        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($entity_class_name, InterfaceWeight::class);

        $entity_id = $request->getParsedBodyParam(CRUDTableWidgetDelete::FIELD_OBJECT_ID);
        Assert::assert($entity_id);

        $context_fields_names_str = $request->getParsedBodyParam(
            CRUDTableWidgetWeight::FORMFIELD_CONTEXT_FIELDS_NAME,
            ''
        );
        $context_fields_names_arr = [];
        if ($context_fields_names_str != '') {
            $context_fields_names_arr = explode(',', $context_fields_names_str);
        }

        $context_arr = [];
        foreach ($context_fields_names_arr as $context_field_name) {
            $context_arr[$context_field_name] = NullablePostFields::optionalFieldValue($request, $context_field_name);
        }

        $this->crud->swapWeights($entity_class_name, $entity_id, $context_arr);

        return $response->withRedirect($request->getUri());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function updateEntityFieldOperation(Request $request, Response $response): ?Response
    {
        $table_id_from_request = $request->getParsedBodyParam(CRUDTable::FIELD_CRUDTABLE_ID, '');
        // проверяем, что операция выполняется для таблицы из запроса, потому что класс модели мы берем из таблицы
        if ($table_id_from_request != $this->table_id) {
            return  null;
        }

        $entity_field_name = $request->getParsedBodyParam(CRUDTable::FIELD_FIELD_NAME);
        Assert::assert(!is_null($entity_field_name));

        $value = $request->getParsedBodyParam(CRUDTable::FIELD_FIELD_VALUE);
        Assert::assert(!is_null($value));

        $entity_id = $request->getParsedBodyParam(CRUDTable::FIELD_ENTITY_ID);
        Assert::assert(!is_null($entity_id));

        // @TODO: owner check!!!

        $obj = $this->crud->createAndLoadObject($this->entity_class_name, $entity_id);

        $reflect = new \ReflectionClass($obj);

        $property_obj = $reflect->getProperty($entity_field_name);
        $property_obj->setAccessible(true);
        $property_obj->setValue($obj, $value);

        $this->crud->saveObject($obj);

        return $response->withRedirect($request->getUri());
    }

    /**
     * @param Request $request
     * @param InterfaceCRUDTableFilter[] $filters_arr
     * @return string
     * @throws \Exception
     */
    protected static function filtersHtml(Request $request, array $filters_arr): string
    {
        $html = '';

        if ($filters_arr) {
            $html .= '<div class="">';
            $html .= '<form class="filters-form form-horizontal">';
            $html .= '<div class="row">';

            //$filter_index = 0;

            foreach ($filters_arr as $filter_obj) {
                //@TODO check class implements InterfaceCRUDTableFilter

                if ($filter_obj instanceof InterfaceCRUDTableFilterVisible) {
                    $html .= '<div class="col-md-12">';
                    $html .= '<div class="form-group">';

                    $html .= '<label class="col-sm-4 text-right control-label">' . $filter_obj->getTitle() . '</label>';
                    $html .= '<div class="col-sm-8">' . $filter_obj->getHtml($request) . '</div>';

                    $html .= '</div>';
                    $html .= '</div>';
                } elseif ($filter_obj instanceof InterfaceCRUDTableFilterInvisible) {
                    // do nothing with invisible filters
                } else {
                    throw new \Exception('filter doesnt implement interface ...');
                }
            }

            $html .= '</div>';
            $html .= '</form>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param Request $request
     * @param string $table_index_on_page
     * @param InterfaceCRUDTableFilter[] $filters_arr
     * @param string $create_form_html
     * @return string
     */
    public static function filtersAndCreateButtonHtmlInline(
        Request $request,
        string $table_index_on_page,
        array $filters_arr,
        string $create_form_html = ''
    ): string {
        if (empty($filters_arr) && ($create_form_html == '')) {
            return '';
        }

        $html = HTML::div('filters-inline', '', function () use (
            $request,
            $table_index_on_page,
            $filters_arr,
            $create_form_html
        ) {
            echo '<style>.filters-inline {margin-bottom: 10px;}</style>';

            if (!empty($filters_arr)) {
                echo '<form class="filters-form" style="display: inline;">';

                foreach ($filters_arr as $filter_obj) {
                    //@TODO check class implements InterfaceCRUDTableFilter

                    if ($filter_obj instanceof InterfaceCRUDTableFilterVisible) {
                        echo '<div style="display: inline-block; margin-right: 10px;">';

                        if ($filter_obj->getTitle()) {
                            echo '<label style="display: inline-block; margin-top: 5px; margin-right: 5px;">' .
                                $filter_obj->getTitle() . '</label>';
                        }

                        echo '<span style="display: inline-block;">' . $filter_obj->getHtml($request) . '</span>';
                        echo '</div>';
                    } elseif ($filter_obj instanceof InterfaceCRUDTableFilterInvisible) {
                        // do nothing with invisible filters
                    } else {
                        throw new \Exception('filter doesnt implement interface ...');
                    }
                }

                echo '</form>';
            }

            if ($create_form_html != '') {
                $create_form_element_id = 'collapse_' . rand(1, 999999);
                echo MagnificPopup::button($create_form_element_id, 'btn btn-sm btn-primary pull-right', '<span class="glyphicon glyphicon-plus"></span> Создать');
                echo MagnificPopup::popupHtml($create_form_element_id, $create_form_html);
            }
        });

        return $html;
    }

    /**
     * @param string $table_index_on_page
     * @param string $create_form_html
     * @param InterfaceCRUDTableFilter[] $filters_arr
     * @return string
     */
    protected static function toolbarHtml(
        string $table_index_on_page,
        string $create_form_html,
        array $filters_arr
    ): string {
        if ($create_form_html == '') {
            return '';
        }

        $html = '';

        $create_form_element_id = 'collapse_' . rand(1, 999999);

        $html .= '<div class="btn-group" role="group">';
        if ($create_form_html) {
            $html .= '<button type="button" class="btn btn-default" data-toggle="collapse" data-target="#' .
                $create_form_element_id . '">Создать</button>';
        }
        $html .= '</div>';

        if ($create_form_html) {
            $html .= '<div class="collapse" id="' . $create_form_element_id . '"><div class="well">' .
                $create_form_html . '</div></div>';
            $html .= '<script>' .
                '$("#' . $create_form_element_id . '")
                    .on("shown.bs.collapse", function () {$(this).find(".form-control").eq(0).focus();})' .
                '</script>';
        }

        return $html;
    }
}
