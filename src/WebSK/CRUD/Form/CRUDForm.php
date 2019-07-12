<?php

namespace WebSK\CRUD\Form;

use OLOG\CheckClassInterfaces;
use OLOG\Url;
use WebSK\Entity\InterfaceEntity;
use WebSK\Utils\Assert;
use OLOG\HTML;
use OLOG\Operations;
use WebSK\Utils\Messages;
use WebSK\Utils\Sanitize;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Utils\HTTP;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;

/**
 * Class CRUDForm
 * @package WebSK\CRUD
 */
class CRUDForm
{
    const OPERATION_SAVE_EDITOR_FORM = 'OPERATION_SAVE_EDITOR_FORM';
    const OPERATION_DELETE_ENTITY = 'OPERATION_DELETE_ENTITY';

    const FIELD_CLASS_NAME = '_FIELD_CLASS_NAME';
    const FIELD_OBJECT_ID = '_FIELD_OBJECT_ID';
    const FIELD_REDIRECT_URL = '_FIELD_REDIRECT_URL';
    const FIELD_REDIRECT_URL_GET_PARAMS = '_FIELD_REDIRECT_URL_GET_PARAMS';
    const FIELD_FORM_ID = '_FIELD_FORM_ID';

    /** @var CRUD */
    protected $crud;
    /** @var object */
    protected $obj;
    /** @var InterfaceCRUDFormRow[] */
    protected $element_obj_arr;
    /** @var string */
    protected $url_to_redirect_after_operation = '';
    /** @var array */
    protected $redirect_get_params_arr = [];
    /** @var string */
    protected $form_unique_id;
    /** @var string */
    protected $operation_code = self::OPERATION_SAVE_EDITOR_FORM;
    /** @var bool */
    protected $hide_submit_button = false;

    /** @var string */
    protected $submit_button_title = 'Сохранить';

    /** @var string */
    protected $submit_button_class = '';

    /**
     * CRUDForm constructor.
     * @param CRUD $crud
     * @param string $form_unique_id
     * @param $obj
     * @param array $element_obj_arr
     * @param string $url_to_redirect_after_operation
     * @param array $redirect_get_params_arr
     * @param string $operation_code
     * @param bool $hide_submit_button
     * @param string $submit_button_title
     * @param string $submit_button_class
     */
    public function __construct(
        CRUD $crud,
        string $form_unique_id,
        $obj,
        array $element_obj_arr,
        string $url_to_redirect_after_operation = '',
        array $redirect_get_params_arr = [],
        string $operation_code = self::OPERATION_SAVE_EDITOR_FORM,
        bool $hide_submit_button = false,
        string $submit_button_title = 'Сохранить',
        string $submit_button_class = 'btn btn-primary'
    ) {
        $this->crud = $crud;
        $this->form_unique_id = $form_unique_id;
        $this->obj = $obj;
        $this->element_obj_arr = $element_obj_arr;
        $this->url_to_redirect_after_operation = $url_to_redirect_after_operation;
        $this->redirect_get_params_arr = $redirect_get_params_arr;
        $this->operation_code = $operation_code;
        $this->hide_submit_button = $hide_submit_button;
        $this->submit_button_title = $submit_button_title;
        $this->submit_button_class = $submit_button_class;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function processRequest(Request $request, Response $response): ?Response
    {
        if ($request->getMethod() != HTTP::METHOD_POST) {
            return null;
        }

        $form_id = $request->getParsedBodyParam(self::FIELD_FORM_ID);
        if ($form_id != $this->form_unique_id) {
            return null;
        }

        $operation_code = $request->getParsedBodyParam(Operations::FIELD_NAME_OPERATION_CODE);

        switch ($operation_code) {
            case self::OPERATION_DELETE_ENTITY:
                return $this->deleteEntityOperation($request, $response);
            case self::OPERATION_SAVE_EDITOR_FORM:
                return $this->saveEditorFormOperation($request, $response);
        }

        return $response->withRedirect($request->getUri());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \ReflectionException
     */
    protected function saveEditorFormOperation(Request $request, Response $response): Response
    {
        $url_to_redirect_after_save = $request->getParsedBodyParam(CRUDForm::FIELD_REDIRECT_URL, '');
        $url_to_redirect_after_save = parse_url($url_to_redirect_after_save,  PHP_URL_PATH);
        $redirect_get_params_str = $request->getParsedBodyParam(CRUDForm::FIELD_REDIRECT_URL_GET_PARAMS, '');
        $redirect_get_params_arr = [];
        parse_str($redirect_get_params_str, $redirect_get_params_arr);

        $obj = $this->crud->saveOrUpdateObjectFromFormRequest($this->obj, $request);

        Messages::setMessage('Изменения сохранены');

        if ($url_to_redirect_after_save != '') {
            $redirect_url = $url_to_redirect_after_save;
            $redirect_url = $this->crud->compile($redirect_url, ['this' => $obj]);

            $params_arr = [];
            foreach ($redirect_get_params_arr as $param => $value) {
                $params_arr[$param] = $this->crud->compile($value, ['this' => $obj]);
            }

            if (!empty($redirect_get_params_arr)) {
                $redirect_url = $redirect_url . '?' . http_build_query($params_arr);
            }

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
    protected function deleteEntityOperation(Request $request, Response $response): Response
    {
        $entity_class_name = get_class($this->obj);
        Assert::assert($entity_class_name);

        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($entity_class_name, InterfaceEntity::class);

        $entity_id = $this->obj->getId();
        Assert::assert($entity_id);

        $this->crud->deleteObject($entity_class_name, $entity_id);

        Messages::setMessage('Удаление выполнено успешно');

        $url_to_redirect_after_delete = $request->getParsedBodyParam(CRUDForm::FIELD_REDIRECT_URL, '');
        $url_to_redirect_after_delete = parse_url($url_to_redirect_after_delete,  PHP_URL_PATH);
        if ($url_to_redirect_after_delete != '') {
            $redirect_get_params_str = $request->getParsedBodyParam(CRUDForm::FIELD_REDIRECT_URL_GET_PARAMS, '');
            $redirect_get_params_arr = [];
            parse_str($redirect_get_params_str, $redirect_get_params_arr);

            $redirect_url = $url_to_redirect_after_delete;

            $params_arr = [];
            foreach ($redirect_get_params_arr as $param => $value) {
                $params_arr[$param] = $value;
            }

            if (!empty($redirect_get_params_arr)) {
                $redirect_url = $redirect_url . '?' . http_build_query($params_arr);
            }

            return $response->withRedirect($redirect_url);
        }

        return $response->withRedirect($request->getUri());
    }

    /**
     * ид объекта может быть пустым - тогда при сохранении формы создаст новый объект
     * @return string
     * @throws \Exception
     */
    public function html(): string
    {
        $form_element_id = 'formElem_' . uniqid();
        if ($this->form_unique_id) {
            $form_element_id = $this->form_unique_id;
        }

        $html = '';
        $html .= Operations::operationCodeHiddenField($this->operation_code);
        $html .= '<input type="hidden" name="' . self::FIELD_CLASS_NAME . '" '.
            'value="' . Sanitize::sanitizeAttrValue(get_class($this->obj)) . '">';
        $html .= '<input type="hidden" name="' . self::FIELD_OBJECT_ID . '" '.
            'value="' . Sanitize::sanitizeAttrValue(CRUDFieldsAccess::getObjId($this->obj)) . '">';
        $html .= '<input type="hidden" name="' . self::FIELD_REDIRECT_URL . '" '.
            'value="' . Sanitize::sanitizeAttrValue($this->url_to_redirect_after_operation) . '">';
        $html .= '<input type="hidden" name="' . self::FIELD_REDIRECT_URL_GET_PARAMS . '" '.
            'value="' . Sanitize::sanitizeAttrValue(http_build_query($this->redirect_get_params_arr)) . '">';
        $html .= '<input type="hidden" name="' . self::FIELD_FORM_ID . '" '.
            'value="' . Sanitize::sanitizeAttrValue($this->form_unique_id) . '">';

        foreach ($this->element_obj_arr as $element_obj) {
            Assert::assert($element_obj instanceof InterfaceCRUDFormRow);
            $html .= $element_obj->html($this->obj, $this->crud);
        }

        $html .= '<div class="row">';
        $html .= '<div class="col-sm-8 col-sm-offset-4">';
        if (!$this->hide_submit_button) {
            $html .= '<button style="width: 100%" type="submit" class="' . $this->submit_button_class . '">' . $this->submit_button_title . '</button>';
        }
        $html .= '</div>';
        $html .= '</div>';

        $form_html = HTML::tag('form', [
            'id' => $form_element_id,
            'class' => 'form-horizontal',
            'role' => 'form',
            'action' => Url::getCurrentUrl(), // Иначе при поиске из CRUDFormWidgetReferenceAjax window.location изменяется
            'method' => 'post'
        ], $html);

        // Загрузка скриптов
        $form_html .= CRUDFormScript::getHtml($form_element_id);

        return $form_html;
    }
}
