<?php

namespace WebSK\CRUD\Table;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Utils\Assert;
use OLOG\CheckClassInterfaces;
use OLOG\HTML;
use OLOG\MagnificPopup;
use OLOG\Operations;
use WebSK\Entity\InterfaceWeight;
use WebSK\Utils\HTTP;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDForm;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetWeight;
use WebSK\CRUD\Pager;
use WebSK\Utils\Messages;

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

    const CREATE_BUTTON_POSITION_LEFT_POPUP = 'CREATE_BUTTON_POSITION_LEFT_POPUP';
    const CREATE_BUTTON_POSITION_RIGHT_POPUP = 'CREATE_BUTTON_POSITION_RIGHT_POPUP';
    const CREATE_BUTTON_POSITION_LEFT_TOOLBAR = 'CREATE_BUTTON_POSITION_LEFT_TOOLBAR';
    const CREATE_BUTTON_POSITION_RIGHT_TOOLBAR = 'CREATE_BUTTON_POSITION_RIGHT_TOOLBAR';
    const CREATE_BUTTON_POSITION_NONE = 'CREATE_BUTTON_POSITION_NONE';
    const CREATE_BUTTON_TEXT = 'Создать';

    const FIELD_CRUDTABLE_ID = 'crudtable_id';
    const FIELD_FIELD_NAME = 'field_name';
    const FIELD_FIELD_VALUE = 'field_value';
    const FIELD_ENTITY_ID = 'entity_id';

    const FIELD_NAME_CRUD_TABLE_ID = '_FIELD_NAME_CRUD_TABLE_ID';
    const FIELD_NAME_ENTITY_CLASS_NAME = '_FIELD_NAME_ENTITY_CLASS_NAME';

    const CSV_COLUMN_DELIMITER = ';';

    protected CRUD $crud;

    protected string $entity_class_name;

    protected ?CRUDForm $create_form_obj = null;

    /** @var InterfaceCRUDTableColumn[] */
    protected array $column_obj_arr;

    /** @var InterfaceCRUDTableFilter[] */
    protected array $filters_arr;

    protected string $order_by = '';

    protected string $table_id = '';

    protected string $filters_position = self::FILTERS_POSITION_NONE;

    protected bool $display_total_rows_count = false;

    protected int $page_size = CRUD::DEFAULT_PAGE_SIZE;

    protected string $create_button_position = self::CREATE_BUTTON_POSITION_NONE;

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
     * @param string $create_button_position
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
        int $page_size = CRUD::DEFAULT_PAGE_SIZE,
        string $create_button_position = self::CREATE_BUTTON_POSITION_NONE
    )
    {
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
        $this->setCreateButtonPosition($create_button_position);
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function html(ServerRequestInterface $request): string
    {
        // вывод таблицы

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

            if ($this->create_form_obj) {
                $create_form_html = $this->create_form_obj->html();
                echo $this->createFormHtml($create_form_html);
            }

            if ($this->filters_position == self::FILTERS_POSITION_TOP) {
                echo self::filtersHtml($request, $this->filters_arr);
            }

            if ($this->filters_position == self::FILTERS_POSITION_INLINE) {
                echo self::filtersHtmlInline(
                    $request,
                    $this->table_id,
                    $this->filters_arr
                );
            }

            echo '<div class="table-responsive">';

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

            echo '</div>';

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
     * @param ServerRequestInterface $request
     * @param string $column_delimiter
     * @param int $total_rows_count
     * @return string
     */
    public function csv(ServerRequestInterface $request, string $column_delimiter = self::CSV_COLUMN_DELIMITER, int &$total_rows_count = 0): string
    {
        $tsv = '';

        $objs_ids_arr = $this->crud->getObjIdsArrForClassName(
            $request,
            $this->entity_class_name,
            $this->filters_arr,
            $this->order_by,
            100000,
            0,
            $this->display_total_rows_count,
            $total_rows_count
        );

        $has_nonempty_th = false;
        foreach ($this->column_obj_arr as $column_obj) {
            Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);
            if ($column_obj->getTitle() != '') {
                $has_nonempty_th = true;
            }
        }

        if ($has_nonempty_th) {
            $row_html = '';
            foreach ($this->column_obj_arr as $column_obj) {
                Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);
                $row_html .= $this->csvCellRender((string) $column_obj->getTitle(), $column_delimiter);
            }
            $tsv .= $this->csvRowRender($row_html);
        }

        foreach ($objs_ids_arr as $obj_id) {
            $row_html = '';
            $obj_obj = $this->crud->createAndLoadObject($this->entity_class_name, $obj_id);

            foreach ($this->column_obj_arr as $column_obj) {
                Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);

                $widget_obj = $column_obj->getWidgetObj();
                Assert::assert($widget_obj);
                Assert::assert($widget_obj instanceof InterfaceCRUDTableWidget);
                $row_html .= $this->csvCellRender($widget_obj->html($obj_obj, $this->crud), $column_delimiter);
            }
            $tsv .= $this->csvRowRender($row_html);
        }

        return strip_tags($tsv);
    }

    /**
     * @param string $str
     * @param string $column_delimiter
     * @return string
     */
    protected function csvCellRender(string $str, string $column_delimiter): string
    {
        $str = mb_ereg_replace('@[\R\t]@', ' ', $str);
        return $str . $column_delimiter;
    }

    /**
     * @param string $str
     * @return string
     */
    protected function csvRowRender(string $str): string
    {
        $str = mb_ereg_replace('@\R@', ' ', $str);
        return $str . "\r\n";
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return null|ResponseInterface
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function processRequest(ServerRequestInterface $request, ResponseInterface $response): ?ResponseInterface
    {
        if ($this->create_form_obj instanceof CRUDForm) {
            $crud_form_response = $this->create_form_obj->processRequest($request, $response);
            if ($crud_form_response instanceof ResponseInterface) {
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function deleteEntityOperation(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $entity_class_name = $request->getParsedBodyParam(CRUDTableWidgetDelete::FIELD_CLASS_NAME);
        Assert::assert($entity_class_name);

        $entity_id = $request->getParsedBodyParam(CRUDTableWidgetDelete::FIELD_OBJECT_ID);
        Assert::assert($entity_id);

        $this->crud->deleteObject($entity_class_name, $entity_id);

        Messages::setMessage('Удаление выполнено успешно');

        $redirect_url = $request->getParsedBodyParam(CRUDTableWidgetDelete::FIELD_REDIRECT_AFTER_DELETE_URL, '');
        if ($redirect_url != '') {
            return $response->withRedirect($redirect_url);
        }

        return $response->withRedirect($request->getUri());
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function swapEntityWeightOperation(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function updateEntityFieldOperation(ServerRequestInterface $request, ResponseInterface $response): ?ResponseInterface
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
     * @param ServerRequestInterface $request
     * @param InterfaceCRUDTableFilter[] $filters_arr
     * @return string
     * @throws \Exception
     */
    protected static function filtersHtml(ServerRequestInterface $request, array $filters_arr): string
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
     * @param ServerRequestInterface $request
     * @param string $table_index_on_page
     * @param InterfaceCRUDTableFilter[] $filters_arr
     * @return string
     */
    public static function filtersHtmlInline(
        ServerRequestInterface $request,
        string $table_index_on_page,
        array $filters_arr
    ): string
    {
        if (empty($filters_arr)) {
            return '';
        }

        $html = HTML::div('filters-inline', '', function () use (
            $request,
            $table_index_on_page,
            $filters_arr
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
        });

        return $html;
    }

    /**
     * @param string $create_form_html
     * @param string $create_button_position
     * @param bool $table_has_filters
     * @return string
     */
    protected static function createButtonPopupHtml(
        string $create_form_html,
        string $create_button_position,
        bool $table_has_filters = true
    ) : string {
        $button_create_position_class_name = '';
        switch ($create_button_position) {
            case self::CREATE_BUTTON_POSITION_LEFT_POPUP:
                $button_create_position_class_name = ' pull-left';
                break;
            case self::CREATE_BUTTON_POSITION_RIGHT_POPUP:
                $button_create_position_class_name = ' pull-right';
                break;
        }

        $html = '';

        $create_form_element_id = 'collapse_' . rand(1, 999999);
        $html .= MagnificPopup::button(
            $create_form_element_id,
            'btn btn-sm btn-default' . $button_create_position_class_name,
            self::CREATE_BUTTON_TEXT
        );
        $html .= MagnificPopup::popupHtml($create_form_element_id, $create_form_html);

        if (!$table_has_filters) {
            $html .= '<div style="display: inline-block; width: 100%"></div>';
        }

        return $html;
    }

    /**
     * @param string $create_form_html
     * @param string $create_position_button
     * @return string
     */
    protected static function createButtonToolbarHtml(
        string $create_form_html,
        string $create_position_button
    ): string {
        $html = '';

        $button_create_position_class_name = '';
        switch ($create_position_button) {
            case self::CREATE_BUTTON_POSITION_LEFT_TOOLBAR:
                $button_create_position_class_name = ' pull-left';
                break;
            case self::CREATE_BUTTON_POSITION_RIGHT_TOOLBAR:
                $button_create_position_class_name = ' pull-right';
                break;
        }

        $create_form_element_id = 'collapse_' . rand(1, 999999);
        $create_from_btn_group_id = '#btn-group-' . $create_form_element_id;

        $html .= '<style>' . $create_from_btn_group_id . ' { margin-bottom: 10px; }</style>';
        $html .= '<div class="btn-group' . $button_create_position_class_name . '" role="group">';
        if ($create_form_html) {
            $html .=
                '<button 
                    type="button" 
                    class="btn btn-default" 
                    data-toggle="collapse" 
                    data-target="#' . $create_form_element_id . '"
                >' . self::CREATE_BUTTON_TEXT . '</button>';
        }
        $html .= '</div>';

        if ($create_form_html) {
            $html .= '<br /><br />';
            $html .= '<div class="collapse" id="' . $create_form_element_id . '"><div class="well">' .
                $create_form_html . '</div></div>';
            $html .= '<script>' .
                '$("#' . $create_form_element_id . '")
                    .on("shown.bs.collapse", function () {$(this).find(".form-control").eq(0).focus();})' .
                '</script>';
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getCreateButtonPosition(): string
    {
        return $this->create_button_position;
    }

    /**
     * @param string $create_button_position
     */
    public function setCreateButtonPosition(string $create_button_position): void
    {
        $this->create_button_position = $create_button_position;
    }

    /**
     * @param string $create_form_html
     * @return string
     */
    protected function createFormHtml(string $create_form_html) : string
    {
        $table_has_visible_filters = $this->tableHasVisibleFilters();

        if ($this->getCreateButtonPosition() == self::CREATE_BUTTON_POSITION_NONE) {
            if (!$table_has_visible_filters
                ||$this->filters_position == self::FILTERS_POSITION_INLINE) {
                $this->setCreateButtonPosition(self::CREATE_BUTTON_POSITION_RIGHT_POPUP);
            } else {
                $this->setCreateButtonPosition(self::CREATE_BUTTON_POSITION_LEFT_TOOLBAR);
            }
        }

        if ($this->getCreateButtonPosition() == self::CREATE_BUTTON_POSITION_LEFT_POPUP
            || $this->getCreateButtonPosition() == self::CREATE_BUTTON_POSITION_RIGHT_POPUP
        ) {
            return self::createButtonPopupHtml(
                $create_form_html,
                $this->getCreateButtonPosition(),
                $table_has_visible_filters
            );
        }

        if ($this->getCreateButtonPosition() == self::CREATE_BUTTON_POSITION_LEFT_TOOLBAR
            || $this->getCreateButtonPosition() == self::CREATE_BUTTON_POSITION_RIGHT_TOOLBAR) {
            return self::createButtonToolbarHtml(
                $create_form_html,
                $this->getCreateButtonPosition()
            );
        }

        return '';
    }

    /**
     * @return bool
     */
    protected function tableHasVisibleFilters() : bool
    {
        if (!$this->filters_arr) {
            return false;
        }

        foreach ($this->filters_arr as $filter_obj) {
            if ($filter_obj instanceof InterfaceCRUDTableFilterInvisible) {
                continue;
            }

            return true;
        }

        return false;
    }
}
