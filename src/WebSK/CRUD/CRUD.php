<?php

namespace WebSK\CRUD;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\Table\CRUDTableJSON;
use WebSK\Entity\EntityRepository;
use WebSK\Entity\EntityService;
use WebSK\Entity\WeightService;
use WebSK\Utils\Assert;
use Psr\Container\ContainerInterface;
use WebSK\Entity\InterfaceEntity;
use WebSK\Entity\InterfaceWeight;
use WebSK\CRUD\Form\CRUDForm;
use WebSK\CRUD\Form\InterfaceCRUDFormRow;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\InterfaceCRUDTableColumn;
use WebSK\CRUD\Table\InterfaceCRUDTableFilter;
use WebSK\Utils\Sanitize;

/**
 * Class CRUD
 * @package WebSK\CRUD
 */
class CRUD
{
    const int DEFAULT_PAGE_SIZE = 100;
    const string NULL_STRING = 'NULLSTRING';
    const string IS_NULL_POSTFIX = '___is_null';

    protected ContainerInterface $container;

    /**
     * CRUD constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * CRUDTable constructor.
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
     * @return CRUDTable
     */
    public function createTable(
        string $entity_class_name,
        ?CRUDForm $create_form_obj = null,
        array $column_obj_arr = [],
        array $filters_arr = [],
        string $order_by = '',
        string $table_id = '',
        string $filters_position = CRUDTable::FILTERS_POSITION_NONE,
        bool $display_total_rows_count = false,
        int $page_size = self::DEFAULT_PAGE_SIZE,
        string $create_button_position = CRUDTable::CREATE_BUTTON_POSITION_NONE
    ): CRUDTable
    {
        return new CRUDTable(
            $this,
            $entity_class_name,
            $create_form_obj,
            $column_obj_arr,
            $filters_arr,
            $order_by,
            $table_id,
            $filters_position,
            $display_total_rows_count,
            $page_size,
            $create_button_position
        );
    }

    /**
     * @param string $entity_class_name
     * @param InterfaceCRUDTableColumn[] $column_obj_arr
     * @param InterfaceCRUDTableFilter[] $filters_arr
     * @param string $order_by
     * @return CRUDTableJSON
     */
    public function createTableJSON(
        string $entity_class_name,
        array $column_obj_arr,
        array $filters_arr = [],
        string $order_by = ''
    ): CRUDTableJSON {
        return new CRUDTableJSON(
            $this,
            $entity_class_name,
            $column_obj_arr,
            $filters_arr,
            $order_by
        );
    }

    /**
     * CRUDForm constructor.
     * @param string $form_unique_id
     * @param $obj
     * @param InterfaceCRUDFormRow[] $element_obj_arr
     * @param string|Closure $url_to_redirect_after_operation
     * @param array $redirect_get_params_arr
     * @param string $operation_code
     * @param bool $hide_submit_button
     * @param string $submit_button_title
     * @param string $submit_button_class
     * @return CRUDForm
     */
    public function createForm(
        string $form_unique_id,
        $obj,
        array $element_obj_arr,
        $url_to_redirect_after_operation = '',
        array $redirect_get_params_arr = [],
        string $operation_code = CRUDOperations::OPERATION_SAVE_EDITOR_FORM,
        bool $hide_submit_button = false,
        string $submit_button_title = 'Сохранить',
        string $submit_button_class = 'btn btn-primary'
    ): CRUDForm
    {
        return new CRUDForm(
            $this,
            $form_unique_id,
            $obj,
            $element_obj_arr,
            $url_to_redirect_after_operation,
            $redirect_get_params_arr,
            $operation_code,
            $hide_submit_button,
            $submit_button_title,
            $submit_button_class
        );
    }

    /**
     * Возвращает одну страницу списка объектов указанного класса.
     * @param ServerRequestInterface $request
     * @param string $entity_class_name Имя класса модели
     * @param InterfaceCRUDTableFilter[] $filters_arr
     * @param string $order_by
     * @param int $page_size
     * @param int $start
     * @param bool $execute_total_rows_count_query
     * @param int $total_rows_count
     * @return array Массив идентификаторов объектов.
     * @throws \Exception
     */
    public function getObjIdsArrForClassName(
        ServerRequestInterface $request,
        string $entity_class_name,
        array $filters_arr,
        string $order_by = '',
        int $page_size = self::DEFAULT_PAGE_SIZE,
        int $start = 0,
        bool $execute_total_rows_count_query = false,
        int &$total_rows_count = 0
    ): array
    {
        $interfaces_arr = class_implements($entity_class_name);
        Assert::assert(
            $interfaces_arr && in_array(InterfaceEntity::class, $interfaces_arr),
            'Class ' . $entity_class_name . ' does not implement interface ' . InterfaceEntity::class
        );

        $repository_container_id = EntityRepository::getContainerIdByClassName($entity_class_name);
        Assert::assert($this->container->has($repository_container_id), 'Unknown repository ' . $repository_container_id);

        /** @var EntityRepository $entity_repository */
        $entity_repository = $this->container->get($repository_container_id);

        $db_id_field_name = CRUDFieldsAccess::getIdFieldName($entity_class_name);

        list($where, $query_param_values_arr) = $this->makeSQLConditionsAndPlaceholderValues($request, $filters_arr);

        $db_table_name = $entity_repository->getTableName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name)
            . ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name);

        if ($where) {
            $query .= ' WHERE ' . $where;
        }

        if ($order_by == '') {
            $order_by = Sanitize::sanitizeSqlColumnName($db_id_field_name);
        }

        $query .= ' ORDER BY ' . $order_by
            . ' LIMIT ' . intval($page_size)
            . ' OFFSET ' . intval($start);

        $obj_ids_arr = $entity_repository->getDbService()->readColumn(
            $query,
            $query_param_values_arr
        );

        if ($execute_total_rows_count_query) {
            $total_rows_count = $this->getTotalRowsCount($request, $entity_class_name, $filters_arr);
        }

        return $obj_ids_arr;
    }

    /**
     * @param ServerRequestInterface $request
     * @param InterfaceCRUDTableFilter[] $filters_arr
     * @return array
     * @throws \Exception
     */
    protected function makeSQLConditionsAndPlaceholderValues(ServerRequestInterface $request, array $filters_arr): array
    {
        $where = '';
        $query_param_values_arr = [];

        foreach ($filters_arr as $filter_obj) {
            $interfaces_arr = class_implements(get_class($filter_obj));
            Assert::assert(
                $interfaces_arr && in_array(InterfaceCRUDTableFilter::class, $interfaces_arr),
                'Class ' . get_class($filter_obj) . ' does not implement interface ' . InterfaceCRUDTableFilter::class
            );

            list($filter_sql_condition, $filter_placeholder_values_arr) =
                $filter_obj->sqlConditionAndPlaceholderValue($request);

            if ($filter_sql_condition != '') {
                if ($where) {
                    $where .= ' AND ';
                }
                $where .= $filter_sql_condition;
            }

            $query_param_values_arr = array_merge($query_param_values_arr, $filter_placeholder_values_arr);
        }

        return [$where, $query_param_values_arr];
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $entity_class_name
     * @param InterfaceCRUDTableFilter[] $filters_arr
     * @return int
     * @throws \Exception
     */
    protected function getTotalRowsCount(ServerRequestInterface $request, string $entity_class_name, array $filters_arr): int
    {
        $repository_container_id = EntityRepository::getContainerIdByClassName($entity_class_name);
        Assert::assert($this->container->has($repository_container_id), 'Unknown repository ' . $repository_container_id);

        /** @var EntityRepository $entity_repository */
        $entity_repository = $this->container->get($repository_container_id);

        $db_id_field_name = CRUDFieldsAccess::getIdFieldName($entity_class_name);

        $db_table_name = $entity_repository->getTableName();

        list($where, $query_param_values_arr) = $this->makeSQLConditionsAndPlaceholderValues($request, $filters_arr);

        $rows_count_query = 'SELECT count(' . Sanitize::sanitizeSqlColumnName($db_id_field_name) . ')'
            .' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name);
        if ($where) {
            $rows_count_query .= ' WHERE ' . $where;
        }

        $total_rows_count = $entity_repository->getDbService()->readField(
            $rows_count_query,
            $query_param_values_arr
        );

        if ($total_rows_count === false) {
            return 0;
        }

        return $total_rows_count;
    }

    /**
     * @param InterfaceEntity $obj
     * @param ServerRequestInterface $request
     * @return null|InterfaceEntity
     * @throws \ReflectionException
     */
    public function saveOrUpdateObjectFromFormRequest(InterfaceEntity $entity_obj, ServerRequestInterface $request): ?InterfaceEntity
    {
        $post_fields_arr = (array)$request->getParsedBody();

        $new_prop_values_arr = [];
        $null_fields_arr = [];
        $reflect = new \ReflectionClass($entity_obj);

        foreach ($reflect->getProperties() as $prop_obj) {
            // игнорируем статические свойства класса - они относятся не к объекту,
            // а только к классу (http://www.php.net/manual/en/language.oop5.static.php),
            // и в них хранятся настройки ActiveRecord и CRUD
            if ($prop_obj->isStatic()) {
                continue;
            }

            $prop_name = $prop_obj->getName();

            // чтение возможных NULL
            if (array_key_exists($prop_name . self::IS_NULL_POSTFIX, $post_fields_arr)) {
                if ($post_fields_arr[$prop_name . self::IS_NULL_POSTFIX]) {
                    $null_fields_arr[$prop_name] = 1;
                    continue;
                }
            }

            if (!array_key_exists($prop_name, $post_fields_arr) && !$prop_obj->isInitialized($entity_obj)) {
                throw new \Exception('Поле ' . $prop_name . ' является обязательным');
            }
            if (!array_key_exists($prop_name, $post_fields_arr)) {
                continue;
            }

            $new_prop_values_arr[$prop_name] = $post_fields_arr[$prop_name];
        }

        CRUDFieldsAccess::setObjectFieldsFromArray($entity_obj, $new_prop_values_arr, $null_fields_arr);

        $this->getEntityServiceByClassName($entity_obj::class)->save($entity_obj);

        return $entity_obj;
    }

    /**
     * @param string $entity_class_name
     * @param int $entity_id
     * @return InterfaceEntity
     * @throws \Exception
     */
    public function createAndLoadObject(string $entity_class_name, int $entity_id): InterfaceEntity
    {
        /** @var EntityService $entity_service */
        $entity_service = $this->getEntityServiceByClassName($entity_class_name);

        return $entity_service->getById($entity_id);
    }

    /**
     * @param string $entity_class_name
     * @param int $entity_id
     * @throws \Exception
     */
    public function deleteObject(string $entity_class_name, int $entity_id)
    {
        $entity_service = $this->getEntityServiceByClassName($entity_class_name);

        $entity_service->deleteById($entity_id);
    }

    /**
     * @param InterfaceEntity $entity_obj
     * @throws \Exception
     */
    public function saveObject(InterfaceEntity $entity_obj)
    {
        $entity_class_name = get_class($entity_obj);

        $entity_service = $this->getEntityServiceByClassName($entity_class_name);

        $entity_service->save($entity_obj);
    }

    /**
     * @param string $entity_class_name
     * @param int $entity_id
     * @param array $context_arr
     * @throws \Exception
     */
    public function swapWeights(string $entity_class_name, int $entity_id, array $context_arr)
    {
        /** @var InterfaceWeight $obj */
        $obj = $this->createAndLoadObject($entity_class_name, $entity_id);

        $entity_service = $this->getEntityServiceByClassName($entity_class_name);
        Assert::assert($entity_service instanceof WeightService);
        $entity_service->swapWeights($obj, $context_arr);
    }

    /**
     * @param string $entity_class_name
     * @return array
     * @throws \Exception
     */
    public function getAllIdsArr(string $entity_class_name): array
    {
        return $this->getEntityServiceByClassName($entity_class_name)->getAllIdsArrByIdAsc();
    }

    /**
     * @param string $entity_class_name
     * @return EntityService
     * @throws \Exception
     */
    public function getEntityServiceByClassName(string $entity_class_name): EntityService
    {
        $service_container_id = EntityService::getContainerIdByClassName($entity_class_name);
        Assert::assert($service_container_id);

        return $this->container->get($service_container_id);
    }
}
