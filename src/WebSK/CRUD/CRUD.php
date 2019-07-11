<?php

namespace WebSK\CRUD;

use WebSK\Entity\BaseEntityService;
use WebSK\Utils\Assert;
use OLOG\CheckClassInterfaces;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use WebSK\Entity\BaseEntityRepository;
use WebSK\Entity\BaseWeightService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Entity\InterfaceWeight;
use WebSK\CRUD\Form\CRUDForm;
use WebSK\CRUD\Form\InterfaceCRUDFormRow;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\InterfaceCRUDTableColumn;
use WebSK\CRUD\Table\InterfaceCRUDTableFilter;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterInvisible;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterVisible;

class CRUD
{
    const NULL_STRING = 'NULLSTRING';

    /** @var ContainerInterface */
    protected $container;

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
     * @return CRUDTable
     */
    public function createTable(
        string $entity_class_name,
        CRUDForm $create_form_obj = null,
        array $column_obj_arr = [],
        array $filters_arr = [],
        string $order_by = '',
        string $table_id = '',
        string $filters_position = CRUDTable::FILTERS_POSITION_NONE,
        bool $display_total_rows_count = false,
        int $page_size = 30
    ): CRUDTable {
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
            $page_size
        );
    }

    /**
     * CRUDForm constructor.
     * @param string $form_unique_id
     * @param $obj
     * @param InterfaceCRUDFormRow[] $element_obj_arr
     * @param string $url_to_redirect_after_operation
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
        string $url_to_redirect_after_operation = '',
        array $redirect_get_params_arr = [],
        string $operation_code = CRUDForm::OPERATION_SAVE_EDITOR_FORM,
        bool $hide_submit_button = false,
        string $submit_button_title = 'Сохранить',
        string $submit_button_class = 'btn btn-primary'
    ): CRUDForm {
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
     * Сортировка: @TODO.
     *
     * @param Request $request
     * @param string $entity_class_name Имя класса модели
     * @param InterfaceCRUDTableFilter[] $filters_arr
     * @param string $order_by
     * @param int $page_size
     * @param int $start
     * @param bool $execute_total_rows_count_query
     * @param int $total_rows_count
     * @return array Массив идентикаторов объектов.
     * @throws \Exception
     */
    public function getObjIdsArrForClassName(
        Request $request,
        string $entity_class_name,
        array $filters_arr,
        string $order_by = '',
        int $page_size = 30,
        int $start = 0,
        bool $execute_total_rows_count_query = false,
        int &$total_rows_count = 0
    ): array {
        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($entity_class_name, InterfaceEntity::class);

        Assert::assert(!empty($entity_class_name::ENTITY_REPOSITORY_CONTAINER_ID));

        /** @var BaseEntityRepository $entity_repository */
        $entity_repository = $this->container->get($entity_class_name::ENTITY_REPOSITORY_CONTAINER_ID);

        $query_param_values_arr = array();

        $where = ' 1 = 1 ';

        foreach ($filters_arr as $filter_obj) {
            CheckClassInterfaces::exceptionIfClassNotImplementsInterface(
                get_class($filter_obj),
                InterfaceCRUDTableFilter::class
            );

            if ($filter_obj instanceof InterfaceCRUDTableFilterVisible) {
                list($filter_sql_condition, $filter_placeholder_values_arr) =
                    $filter_obj->sqlConditionAndPlaceholderValue($request);
                if ($filter_sql_condition != '') {
                    $where .= ' and ' . $filter_sql_condition;
                }

                $query_param_values_arr = array_merge($query_param_values_arr, $filter_placeholder_values_arr);
            } elseif ($filter_obj instanceof InterfaceCRUDTableFilterInvisible) {
                list($filter_sql_condition, $filter_placeholder_values_arr) =
                    $filter_obj->sqlConditionAndPlaceholderValue($request);
                if ($filter_sql_condition != '') {
                    $where .= ' and ' . $filter_sql_condition;
                }

                $query_param_values_arr = array_merge($query_param_values_arr, $filter_placeholder_values_arr);
            }
        }

        $db_id_field_name = $entity_repository->getIdFieldName();

        if ($order_by == '') { //@TODO sanitize $order_by
            $order_by = $db_id_field_name;
        }

        $db_table_name = $entity_repository->getTableName();

        $obj_ids_arr = $entity_repository->getDbService()->readColumn(
            'select ' . $db_id_field_name . ' from ' . $db_table_name . ' where ' . $where .
            ' order by ' . $order_by . ' limit ' . intval($page_size) . ' offset ' . intval($start),
            $query_param_values_arr
        );

        if ($execute_total_rows_count_query) {
            $total_rows_count = $entity_repository->getDbService()->readField(
                'select count(' . $db_id_field_name . ') from ' . $db_table_name . ' where ' . $where,
                $query_param_values_arr
            );
        }

        return $obj_ids_arr;
    }

    /**
     * @param InterfaceEntity $obj
     * @param Request $request
     * @return null|InterfaceEntity
     * @throws \ReflectionException
     */
    public function saveOrUpdateObjectFromFormRequest($obj, Request $request)
    {
        $entity_class_name = get_class($obj);
        Assert::assert($entity_class_name);

        $object_id = $request->getParsedBodyParam(CRUDForm::FIELD_OBJECT_ID);

        $new_prop_values_arr = [];
        $null_fields_arr = [];
        $reflect = new \ReflectionClass($entity_class_name);

        foreach ($reflect->getProperties() as $prop_obj) {
            // игнорируем статические свойства класса - они относятся не к объекту,
            // а только к классу (http://www.php.net/manual/en/language.oop5.static.php),
            // и в них хранятся настройки ActiveRecord и CRUD
            if (!$prop_obj->isStatic()) {
                $prop_name = $prop_obj->getName();

                $post_fields_arr = $request->getParsedBody();
                // сейчас если поля нет в форме - оно не будет изменено в объекте.
                // это позволяет показывать в форме только часть полей, на остальные форма не повлияет
                if (array_key_exists($prop_name, $post_fields_arr)) {
                    // Проверка на заполнение обязательных полей делается на уровне СУБД, через нот нулл в таблице
                    $new_prop_values_arr[$prop_name] = $_POST[$prop_name];
                }

                // чтение возможных NULL
                if (array_key_exists($prop_name . "___is_null", $post_fields_arr)) {
                    if ($_POST[$prop_name . "___is_null"]) {
                        $null_fields_arr[$prop_name] = 1;
                    }
                }
            }
        }

        $entity_service = $this->getEntityServiceByClassName($entity_class_name);

        $entity_obj = null;
        if ($object_id) {
            $entity_obj = $entity_service->getById($object_id);
        } else {
            $entity_obj = new $entity_class_name;
        }

        $entity_obj = CRUDFieldsAccess::setObjectFieldsFromArray($entity_obj, $new_prop_values_arr, $null_fields_arr);

        $entity_service->save($entity_obj);

        return $entity_obj;
    }

    /**
     * @param string $entity_class_name
     * @param int $entity_id
     * @return InterfaceEntity
     * @throws \Exception
     */
    public function createAndLoadObject(string $entity_class_name, int $entity_id)
    {
        /** @var BaseEntityService $entity_service */
        $entity_service = $this->getEntityServiceByClassName($entity_class_name);

        return $entity_service->getById($entity_id);
    }

    /**
     * @param string $entity_class_name
     * @param $entity_id
     * @throws \Exception
     */
    public function deleteObject(string $entity_class_name, $entity_id)
    {
        $entity_service = $this->getEntityServiceByClassName($entity_class_name);

        $entity_service->deleteById($entity_id);
    }

    /**
     * @param InterfaceEntity $entity_obj
     * @throws \Exception
     */
    public function saveObject($entity_obj)
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
        Assert::assert($entity_service instanceof BaseWeightService);
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
     * @return BaseEntityService
     * @throws \Exception
     */
    protected function getEntityServiceByClassName(string $entity_class_name)
    {
        Assert::assert(!empty($entity_class_name::ENTITY_SERVICE_CONTAINER_ID));

        /** @var BaseEntityService $entity_service */
        $entity_service = $this->container->get($entity_class_name::ENTITY_SERVICE_CONTAINER_ID);

        return $entity_service;
    }

    /**
     * компиляция строки: разворачивание обращений к полям объектов
     * @param string $str
     * @param array $data
     * @return string
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function compile(string $str, array $data): ?string
    {
        // @TODO: clean and finish

        $matches = [];

        // сначала подставляем значения в самых внутренних фигурных скобках,
        // потом которые снаружи, и так пока все скобки не будут заменены
        // поддерживается два вида выражений:
        // - {obj->field} заменяется на значение поля field объекта obj. obj - это ключ массива data,
        // т.е. здесь можно использовать такие строки, которые передаются сюда вызывающими функциями
        // -- обычно виджеты передают объект, который показывается в виджете, с именем this
        // - {class_name.id->field} заменяется на значение поля field объекта класса class_name с идентификатором id
        while (preg_match('@{([^}{]+)}@', $str, $matches)) {
            $expression = $matches[1];
            $replacement = 'UNKNOWN_EXPRESSION';

            $magic_matches = [];
            if (preg_match('@^(\w+)\->([\w()]+)$@', $expression, $magic_matches)) {
                $obj_key_in_data = $magic_matches[1];
                $obj_field_name = $magic_matches[2];

                $obj = $data[$obj_key_in_data];

                $replacement = self::getReplacement($obj, $obj_field_name);
            }

            if (preg_match('@^([\w\\\\]+)\.(\w+)->([\w()]+)$@', $expression, $magic_matches)) {
                $class_name = $magic_matches[1];
                $obj_id = $magic_matches[2];
                $obj_field_name = $magic_matches[3];

                if ($obj_id != self::NULL_STRING) { // TODO: review?
                    $obj = $this->createAndLoadObject($class_name, $obj_id);
                    $replacement = self::getReplacement($obj, $obj_field_name);
                } else {
                    // пустая строка для случаев типа '{' . Sport::class . '.{this->sport_id}->title}'
                    // и this->sport_id не установленно
                    $replacement = '';
                }
            }

            // здесь заменяем только первое вхождение, потому что выше мы обрабатывали только первое вхождение
            // если не сделать это ограничение - вот такое выражение
            // new \VitrinaTV\CRUD\Table\Widgets\CRUDTableWidgetText('{this->video_width}x{this->video_height}'))
            // выдаст "video_width Х video_width"
            // т.е. для прочитает первые скобки, а потом два заменит на результат и первые, и вторые
            $str = preg_replace('@{([^}{]+)}@', $replacement, $str, 1);
        }
        if (self::NULL_STRING == $str) {
            return null;
        }
        return $str;
    }

    /**
     * @param object $obj
     * @param string $obj_field_name
     * @return string
     * @throws \Exception
     * @throws \ReflectionException
     */
    public static function getReplacement($obj, string $obj_field_name): string
    {
        Assert::assert($obj);

        $matches = [];
        // имя поля заканчивается скобками - значит это имя метода
        if (preg_match('@^(\w+)\(\)$@', $obj_field_name, $matches)) {
            $method_name = $matches[1];
            Assert::assert(method_exists($obj, $method_name));
            $replacement = call_user_func([$obj, $method_name]);
        } else {
            $replacement = CRUDFieldsAccess::getObjectFieldValue($obj, $obj_field_name);
        }
        if (is_null($replacement)) {
            $replacement = self::NULL_STRING;
        }

        return $replacement;
    }
}
