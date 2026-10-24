<?php

namespace WebSK\CRUD\Form;

use Closure;
use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDCompiler;
use WebSK\CRUD\CRUDHtml;
use WebSK\CRUD\CRUDOperations;
use WebSK\CRUD\RequestParams;
use WebSK\Entity\InterfaceEntity;
use WebSK\FileManager\FileManager;
use WebSK\Slim\Redirect;
use WebSK\Slim\Response;
use WebSK\Utils\Assert;
use WebSK\Utils\Messages;
use WebSK\Utils\Sanitize;
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

    /** @var list<InterfaceCRUDFormRow> */
    protected array $element_obj_arr;

    /** @var string|Closure(InterfaceEntity): string */
    protected string|Closure $url_to_redirect_after_operation = '';

    /** @var array<string, scalar|Closure(InterfaceEntity): scalar|null> */
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
     * @param list<InterfaceCRUDFormRow> $element_obj_arr
     * @param string|Closure(InterfaceEntity): string $url_to_redirect_after_operation
     * @param array<string, scalar|Closure(InterfaceEntity): scalar|null> $redirect_get_params_arr
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
        string|Closure $url_to_redirect_after_operation = '',
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
        if ($request->getMethod() != RequestMethodInterface::METHOD_POST) {
            return null;
        }

        $form_id = RequestParams::parsedBody($request, self::FIELD_FORM_ID);

        $operation_code = RequestParams::parsedBody($request, CRUDOperations::FIELD_NAME_OPERATION_CODE);

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
        if ($entity_id === null) {
            throw new \LogicException('Cannot delete a file for an unsaved entity.');
        }

        $entity_service = $this->crud->getEntityServiceByClassName($entity_class_name);
        $entity_obj = self::requireLoadedEntity($entity_service->getById($entity_id), $entity_class_name, $entity_id);

        $field_name = self::parsedBodyString($request, self::FIELD_FIELD_NAME);

        $file_name = CRUDFieldsAccess::getObjectFieldValue($entity_obj, $field_name);
        if (!is_string($file_name)) {
            throw new \UnexpectedValueException('File field must contain a string.');
        }

        $json_arr['file'] = $file_name;

        $storage = self::parsedBodyString($request, self::FIELD_STORAGE);

        $file_manager = new FileManager($storage);

        $target_folder = self::parsedBodyString($request, self::FIELD_TARGET_FOLDER);

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

        $null_fields_arr = [];
        if (is_null($blank_field_value)) {
            $null_fields_arr[] = $field_name;
        }

        CRUDFieldsAccess::setObjectFieldsFromArray($entity_obj, [$field_name => $blank_field_value], $null_fields_arr);

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
        if ($entity_id === null) {
            throw new \LogicException('Cannot upload a file for an unsaved entity.');
        }

        $entity_service = $this->crud->getEntityServiceByClassName($entity_class_name);
        $entity_obj = self::requireLoadedEntity($entity_service->getById($entity_id), $entity_class_name, $entity_id);

        $field_name = self::parsedBodyString($request, self::FIELD_FIELD_NAME);

        $file = $_FILES['file_' . $field_name] ?? null;
        if (!is_array($file)) {
            throw new \UnexpectedValueException('Uploaded file data is missing.');
        }

        $file_name = $file['name'] ?? null;
        if (!is_string($file_name)) {
            throw new \UnexpectedValueException('Uploaded file name is invalid.');
        }

        $json_arr['files'][0] = [
            'name' => $file_name,
        ];

        $storage = self::parsedBodyString($request, self::FIELD_STORAGE);

        $file_manager = new FileManager($storage);

        $target_folder = self::parsedBodyString($request, self::FIELD_TARGET_FOLDER);
        $save_as = self::parsedBodyString($request, self::FIELD_SAVE_AS, '');

        $error = null;
        $file_name = $file_manager->storeUploadedFile($file, $target_folder, $save_as, $error);

        if ($error) {
            $json_arr['files'][0]['error'] = $error;
            return Response::responseWithJson($response, $json_arr);
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

            $redirect_url = self::compiledString($url_to_redirect_after_save, $obj);

            $params_arr = [];
            foreach ($redirect_get_params_arr as $param => $value) {
                $params_arr[$param] = self::compiledScalar($value, $obj);
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
        if ($entity_id === null) {
            throw new \LogicException('Cannot delete an unsaved entity.');
        }

        $entity_service = $this->crud->getEntityServiceByClassName($entity_class_name);
        $obj = self::requireLoadedEntity($entity_service->getById($entity_id), $entity_class_name, $entity_id);

        $this->crud->deleteObject($entity_class_name, $entity_id);

        Messages::setMessage('Удаление выполнено успешно');

        $url_to_redirect_after_delete = $this->url_to_redirect_after_operation;
        if ($url_to_redirect_after_delete != '') {
            $redirect_get_params_arr = $this->redirect_get_params_arr;

            $redirect_url = self::compiledString($url_to_redirect_after_delete, $obj);

            $params_arr = [];
            foreach ($redirect_get_params_arr as $param => $value) {
                $params_arr[$param] = self::compiledScalar($value, $obj);
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
            'value="' . Sanitize::sanitizeAttrValue((string)(CRUDFieldsAccess::getObjId($this->obj) ?? '')) . '">';
        $content_html .= '<input type="hidden" name="' . self::FIELD_FORM_ID . '" '.
            'value="' . Sanitize::sanitizeAttrValue($this->form_unique_id) . '">';

        foreach ($this->element_obj_arr as $element_obj) {
            $content_html .= $element_obj->html($this->obj, $this->crud);
        }

        $content_html .= '<div class="row">';
        $content_html .= '<div class="col-sm-8 offset-sm-4">';
        if (!$this->hide_submit_button) {
            $content_html .= '<button type="submit" class="w-100 ' . $this->submit_button_class . '">' . $this->submit_button_title . '</button>';
        }
        $content_html .= '</div>';
        $content_html .= '</div>';

        $form_html = CRUDHtml::tag('form', [
            'id' => $form_element_id,
            'role' => 'form',
            'action' => is_string($_SERVER['REQUEST_URI'] ?? null) ? $_SERVER['REQUEST_URI'] : '',
            'method' => 'post'
        ], $content_html);

        // Загрузка скриптов
        $form_html .= CRUDFormScript::getHtml($form_element_id);

        return $form_html;
    }

    private static function parsedBodyString(
        ServerRequestInterface $request,
        string $key,
        ?string $default = null
    ): string {
        $value = RequestParams::parsedBody($request, $key, $default);
        if (!is_string($value)) {
            throw new \UnexpectedValueException('Request field "' . $key . '" must be a string.');
        }

        return $value;
    }

    /** @param class-string<InterfaceEntity> $entity_class_name */
    private static function requireLoadedEntity(
        ?InterfaceEntity $entity_obj,
        string $entity_class_name,
        int $entity_id
    ): InterfaceEntity {
        if ($entity_obj === null) {
            throw new \UnexpectedValueException('Entity was not loaded: ' . $entity_class_name . '#' . $entity_id);
        }

        return $entity_obj;
    }

    /** @param string|Closure(InterfaceEntity): string $value */
    private static function compiledString(string|Closure $value, InterfaceEntity $entity_obj): string
    {
        $result = CRUDCompiler::fieldValueOrCallableResult($value, $entity_obj);
        if (!is_string($result)) {
            throw new \UnexpectedValueException('Compiled CRUD value must be a string.');
        }

        return $result;
    }

    private static function compiledScalar(mixed $value, InterfaceEntity $entity_obj): string|int|float|bool|null
    {
        $result = $value instanceof Closure ? $value($entity_obj) : $value;
        if (!is_scalar($result) && $result !== null) {
            throw new \UnexpectedValueException('Compiled CRUD parameter must be scalar or null.');
        }

        return $result;
    }
}
