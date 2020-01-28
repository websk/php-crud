<?php

namespace WebSK\CRUD\Demo\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Demo\CRUDDemoServiceProvider;
use WebSK\CRUD\Demo\DemoUser;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetDate;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTextarea;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTimestamp;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetUpload;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class UserEditHandler
 * @package WebSK\CRUD\Demo\RequestHandlers
 */
class DemoUserEditHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int|null $user_id
     * @return \Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, ?int $user_id = null)
    {
        $demo_user_service = CRUDDemoServiceProvider::getDemoUserService($this->container);
        $demo_user_obj = $demo_user_service->getById($user_id);

        if (!$demo_user_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        $crud_form = CRUDServiceProvider::getCrud($this->container)->createForm(
            'user_edit',
            $demo_user_obj,
            [
                new CRUDFormRow('Имя на сайте', new CRUDFormWidgetInput(DemoUser::_NAME)),
                new CRUDFormRow('Имя', new CRUDFormWidgetInput(DemoUser::_FIRST_NAME)),
                new CRUDFormRow('Фамилия', new CRUDFormWidgetInput(DemoUser::_LAST_NAME)),
                new CRUDFormRow('Email', new CRUDFormWidgetInput(DemoUser::_EMAIL)),
                new CRUDFormRow('Дата рождения', new CRUDFormWidgetDate(DemoUser::_BIRTHDAY)),
                new CRUDFormRow('Комментарий', new CRUDFormWidgetTextarea(DemoUser::_COMMENT)),
                new CRUDFormRow('Создано', new CRUDFormWidgetTimestamp(DemoUser::_CREATED_AT_TS)),
                new CRUDFormRow('Фото', new CRUDFormWidgetUpload(DemoUser::_PHOTO,  'images' . DIRECTORY_SEPARATOR . 'users', CRUDFormWidgetUpload::FILE_TYPE_IMAGE))
            ]
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редактирование профиля');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/'),
            new BreadcrumbItemDTO('Пользователи', '/users'),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.main'), $layout_dto);
    }
}
