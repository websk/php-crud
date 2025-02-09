<?php

namespace WebSK\CRUD\Form;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDCompiler;
use WebSK\CRUD\CRUDHtml;
use WebSK\CRUD\CRUDOperations;
use WebSK\Entity\InterfaceEntity;
use WebSK\FileManager\FileManager;
use WebSK\Slim\Redirect;
use WebSK\Slim\Request;
use WebSK\Slim\Response;
use WebSK\Utils\Assert;
use WebSK\Utils\Messages;
use WebSK\Utils\Sanitize;
use WebSK\Utils\HTTP;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;

/**
 * Class CRUDForm
 * @package WebSK\CRUD
 */
class CRUDForm
{

    const string FIELD_CLASS_NAME = '_FIELD_CLASS_NAME';
    const string FIELD_OBJECT_ID = '_FIELD_OBJECT_ID';
    const string FIELD_REDIRECT_URL = '_FIELD_REDIRECT_URL';
    const string FIELD_REDIRECT_URL_GET_PARAMS = '_FIELD_REDIRECT_URL_GET_PARAMS';
    const string FIELD_FORM_ID = '_FIELD_FORM_ID';

    const string FIELD_STORAGE = 'storage';
    const string FIELD_TARGET_FOLDER = 'target_folder';
    const string FIELD_FIELD_NAME = 'field_name';
    const string FIELD_SAVE_AS = 'save_as';

    protected CRUD $crud;

    protected InterfaceEntity $obj;

    /** @var InterfaceCRUDFormRow[] */
    protected array $element_obj_arr;

    /** @var string|Closure */
    protected $url_to_redirect_after_operation = '';

    protected array $redirect_get_params_arr = [];

    protected string $form_unique_id;

    protected string $operation_code = CRUDOperations::OPERATION_SAVE_EDITOR_FORM;

    protected bool $hide_submit_button = false;

    protected string $submit_button_title = 'Сохранить';

    protected string $submit_button_class = '';

    /**
     * CRUDForm constructor.
     * @param CRUD $crud
     * @param string $form_unique_id
     * @param InterfaceEntity $entity_obj
     * @param array $element_obj_arr
     * @param string|Closure $url_to_redirect_after_operation
     * @param array $redirect_get_params_arr
     * @param string $operation_code
     * @param bool $hide_submit_button
     * @param string $submit_button_title
     * @param string $submit_button_class
     */
    public function __construct(
        CRUD $crud,
        string $form_unique_id,
        InterfaceEntity $entity_obj,
        array $element_obj_arr,
        $url_to_redirect_after_operation = '',
        array $redirect_get_params_arr = [],
        string $operation_code = CRUDOperations::OPERATION_SAVE_EDITOR_FORM,
        bool $hide_submit_button = false,
        string $submit_button_title = 'Сохранить',
        string $submit_button_class = 'btn btn-primary'
    )
    {
        $this->crud = $crud;
        $this->form_unique_id = $form_unique_id;
        $this->obj = $entity_obj;
        $this->element_obj_arr = $element_obj_arr;
        $this->url_to_redirect_after_operation = $url_to_redirect_after_operation;
        $this->redirect_get_params_arr = $redirect_get_params_arr;
        $this->operation_code = $operation_code;
        $this->hide_submit_button = $hide_submit_button;
        $this->submit_button_title = $submit_button_title;
        $this->submit_button_class = $submit_button_class;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function processRequest(ServerRequestInterface $request, ResponseInterface $response): ?ResponseInterface
    {
        if ($request->getMethod() != HTTP::METHOD_POST) {
            return null;
        }

        $form_id = Request::getParsedBodyParam($request, self::FIELD_FORM_ID);

        $operation_code = Request::getParsedBodyParam($request, CRUDOperations::FIELD_NAME_OPERATION_CODE);

        switch ($operation_code) {
            case CRUDOperations::OPERATION_DELETE_ENTITY:
                if ($form_id != $this->form_unique_id) {
                    return null;
                }
                return $this->deleteEntityOperation($request, $response);
            case CRUDOperations::OPERATION_SAVE_EDITOR_FORM:
                if ($form_id != $this->form_unique_id) {
                    return null;
                }
                return $this->saveEditorFormOperation($request, $response);
            case CRUDOperations::OPERATION_UPLOAD_FILE:
                return $this->uploadFileFormOperation($request, $response);
            case CRUDOperations::OPERATION_DELETE_FILE:
                return $this->deleteFileFormOperation($request, $response);
        }

        if ($form_id != $this->form_unique_id) {
            return null;
        }

        return Redirect::redirect($response, $request->getUri());
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function deleteFileFormOperation(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $entity_class_name = get_class($this->obj);
        Assert::assert($entity_class_name);

        $interfaces_arr = class_implements($entity_class_name);
        Assert::assert(
            $interfaces_arr && in_array(InterfaceEntity::class, $interfaces_arr),
            'Class ' . $entity_class_name . ' does not implement interface ' . InterfaceEntity::class
        );

        $entity_id = $this->obj->getId();
        Assert::assert($entity_id);

        $entity_service = $this->crud->getEntityServiceByClassName($entity_class_name);
        $entity_obj = $entity_service->getById($entity_id);

        $field_name = Request::getParsedBodyParam($request, self::FIELD_FIELD_NAME);

        $file_name = CRUDFieldsAccess::getObjectFieldValue($entity_obj, $field_name);

        $json_arr['file'] = $file_name;

        $storage = Request::getParsedBodyParam($request, self::FIELD_STORAGE);

        $file_manager = new FileManager($storage);

        $target_folder = Request::getParsedBodyParam($request, self::FIELD_TARGET_FOLDER);

        $file_path = $target_folder . DIRECTORY_SEPARATOR . $file_name;

        if ($file_manager->getStorage()->fileExists($file_path)) {

            try {
                $file_manager->deleteFileIfExist($file_path);
            } catch (\Throwable $exception) {
                $json_arr['error'] = 'Не удалось удалить файл';
            }
        }

        $blank_entity_obj = new $entity_class_name();
        $blank_field_value = CRUDFieldsAccess::getObjectFieldValue($blank_entity_obj, $field_name);

        CRUDFieldsAccess::setObjectFieldsFromArray($entity_obj, [$field_name => $blank_field_value]);
        $entity_service->save($entity_obj);

        return Response::responseWithJson($response, $json_arr);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function uploadFileFormOperation(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $entity_class_name = get_class($this->obj);
        Assert::assert($entity_class_name);

        $interfaces_arr = class_implements($entity_class_name);
        Assert::assert(
            $interfaces_arr && in_array(InterfaceEntity::class, $interfaces_arr),
            'Class ' . $entity_class_name . ' does not implement interface ' . InterfaceEntity::class
        );

        $entity_id = $this->obj->getId();
        Assert::assert($entity_id);

        $entity_service = $this->crud->getEntityServiceByClassName($entity_class_name);
        $entity_obj = $entity_service->getById($entity_id);

        $old_file_name = '';

        $field_name = Request::getParsedBodyParam($request, self::FIELD_FIELD_NAME);

        $file = $_FILES['file_' .  $field_name];

        $file_name = $file['name'];

        $json_arr['files'][0] = [
            'name' => $file_name,
        ];

        $storage = Request::getParsedBodyParam($request, self::FIELD_STORAGE);

        $file_manager = new FileManager($storage);

        $target_folder = Request::getParsedBodyParam($request, self::FIELD_TARGET_FOLDER);
        $save_as = Request::getParsedBodyParam($request, self::FIELD_SAVE_AS, '');

        $file_name = $file_manager->storeUploadedFile($file, $target_folder, $save_as, $error);

        if ($error) {
            $json_arr['files'][0]['error'] = $error;
            return Response::responseWithJson($response, $json_arr);
        }

        if ($old_file_name) {
            $file_manager->deleteFileIfExist($target_folder . DIRECTORY_SEPARATOR .  $old_file_name);
        }

        CRUDFieldsAccess::setObjectFieldsFromArray($entity_obj, [$field_name => $file_name]);

        $entity_service->save($entity_obj);

        return Response::responseWithJson($response, $json_arr);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \ReflectionException
     */
    protected function saveEditorFormOperation(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $entity_class_name = get_class($this->obj);
        Assert::assert($entity_class_name);

        $interfaces_arr = class_implements($entity_class_name);
        Assert::assert(
            $interfaces_arr && in_array(InterfaceEntity::class, $interfaces_arr),
            'Class ' . $entity_class_name . ' does not implement interface ' . InterfaceEntity::class
        );

        $obj = $this->crud->saveOrUpdateObjectFromFormRequest($this->obj, $request);

        Messages::setMessage('Изменения сохранены');

        $url_to_redirect_after_save = $this->url_to_redirect_after_operation;
        if ($url_to_redirect_after_save != '') {
            $redirect_get_params_arr = $this->redirect_get_params_arr;

            $redirect_url = $url_to_redirect_after_save;
            $redirect_url = CRUDCompiler::fieldValueOrCallableResult($redirect_url, $obj);

            $params_arr = [];
            foreach ($redirect_get_params_arr as $param => $value) {
                $params_arr[$param] = CRUDCompiler::fieldValueOrCallableResult($value, $obj);
            }

            if (!empty($redirect_get_params_arr)) {
                $redirect_url = $redirect_url . '?' . http_build_query($params_arr);
            }

            return Redirect::redirect($response, $redirect_url);
        }

        return Redirect::redirect($response, $request->getUri());
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function deleteEntityOperation(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $entity_class_name = get_class($this->obj);
        Assert::assert($entity_class_name);

        $interfaces_arr = class_implements($entity_class_name);
        Assert::assert(
            $interfaces_arr && in_array(InterfaceEntity::class, $interfaces_arr),
            'Class ' . $entity_class_name . ' does not implement interface ' . InterfaceEntity::class
        );

        $entity_id = $this->obj->getId();
        Assert::assert($entity_id);

        $entity_service = $this->crud->getEntityServiceByClassName($entity_class_name);
        $obj = $entity_service->getById($entity_id);

        $this->crud->deleteObject($entity_class_name, $entity_id);

        Messages::setMessage('Удаление выполнено успешно');

        $url_to_redirect_after_delete = $this->url_to_redirect_after_operation;
        if ($url_to_redirect_after_delete != '') {
            $redirect_get_params_arr = $this->redirect_get_params_arr;

            $redirect_url = $url_to_redirect_after_delete;
            $redirect_url = CRUDCompiler::fieldValueOrCallableResult($redirect_url, $obj);

            $params_arr = [];
            foreach ($redirect_get_params_arr as $param => $value) {
                $params_arr[$param] = CRUDCompiler::fieldValueOrCallableResult($value, $obj);
            }

            if (!empty($redirect_get_params_arr)) {
                $redirect_url = $redirect_url . '?' . http_build_query($params_arr);
            }

            return Redirect::redirect($response, $redirect_url);
        }

        return Redirect::redirect($response, $request->getUri());
    }

    /**
     * ID объекта может быть пустым, тогда при сохранении формы создается новый объект
     * @return string
     * @throws \Exception
     */
    public function html(): string
    {
        $form_element_id = 'formElem_' . uniqid();
        if ($this->form_unique_id) {
            $form_element_id = $this->form_unique_id;
        }

        $content_html = '';
        $content_html .= '<input type="hidden" name="' . CRUDOperations::FIELD_NAME_OPERATION_CODE . '" value="' . Sanitize::sanitizeAttrValue($this->operation_code) . '">';

        $content_html .= '<input type="hidden" name="' . self::FIELD_CLASS_NAME . '" '.
            'value="' . Sanitize::sanitizeAttrValue(get_class($this->obj)) . '">';
        $content_html .= '<input type="hidden" name="' . self::FIELD_OBJECT_ID . '" '.
            'value="' . Sanitize::sanitizeAttrValue(CRUDFieldsAccess::getObjId($this->obj)) . '">';
        $content_html .= '<input type="hidden" name="' . self::FIELD_FORM_ID . '" '.
            'value="' . Sanitize::sanitizeAttrValue($this->form_unique_id) . '">';

        foreach ($this->element_obj_arr as $element_obj) {
            Assert::assert($element_obj instanceof InterfaceCRUDFormRow);
            $content_html .= $element_obj->html($this->obj, $this->crud);
        }

        $content_html .= '<div class="row">';
        $content_html .= '<div class="col-sm-8 col-sm-offset-4">';
        if (!$this->hide_submit_button) {
            $content_html .= '<button style="width: 100%" type="submit" class="' . $this->submit_button_class . '">' . $this->submit_button_title . '</button>';
        }
        $content_html .= '</div>';
        $content_html .= '</div>';

        $form_html = CRUDHtml::tag('form', [
            'id' => $form_element_id,
            'class' => 'form-horizontal',
            'role' => 'form',
            'action' => $_SERVER['REQUEST_URI'], // Иначе при поиске из CRUDFormWidgetReferenceAjax window.location изменяется
            'method' => 'post'
        ], $content_html);

        // Загрузка скриптов
        $form_html .= CRUDFormScript::getHtml($form_element_id);

        return $form_html;
    }
}
