<?php

namespace WebSK\CRUD\Demo\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Demo\DemoCompany;
use WebSK\CRUD\Demo\DemoUser;
use WebSK\CRUD\Demo\DemoUserService;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetDate;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTextarea;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTimestamp;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetUpload;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\HTTP;
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $demo_user_id = $args['demo_user_id'];
        $demo_user_service = $this->container->get(DemoUserService::class);
        $demo_user_obj = $demo_user_service->getById($demo_user_id);

        if (!$demo_user_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $crud_form = CRUDServiceProvider::getCrud($this->container)->createForm(
            'user_edit',
            $demo_user_obj,
            [
                new CRUDFormRow('Имя на сайте', new CRUDFormWidgetInput(DemoUser::_NAME)),
                new CRUDFormRow('Имя', new CRUDFormWidgetInput(DemoUser::_FIRST_NAME)),
                new CRUDFormRow('Фамилия', new CRUDFormWidgetInput(DemoUser::_LAST_NAME)),
                new CRUDFormRow('Email', new CRUDFormWidgetInput(DemoUser::_EMAIL)),
                new CRUDFormRow('Телефон', new CRUDFormWidgetInput(DemoUser::_PHONE, true)),
                new CRUDFormRow('Дата рождения', new CRUDFormWidgetDate(DemoUser::_BIRTHDAY)),
                new CRUDFormRow(
                    'Компания',
                    new CRUDFormWidgetReferenceAjax(
                        DemoUser::_COMPANY_ID,
                        DemoCompany::class,
                        DemoCompany::_NAME,
                        $this->pathFor(DemoCompanyListAjaxHandler::class),
                        $this->pathFor(
                            DemoCompanyEditHandler::class,
                            ['demo_company_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                        )
                    )
                ),
                new CRUDFormRow('Комментарий', new CRUDFormWidgetTextarea(DemoUser::_COMMENT)),
                new CRUDFormRow('Создано', new CRUDFormWidgetTimestamp(DemoUser::_CREATED_AT_TS)),
                new CRUDFormRow(
                    'Фото',
                    new CRUDFormWidgetUpload(
                        DemoUser::_PHOTO,
                        'files',
                        'images' . DIRECTORY_SEPARATOR . 'users',
                        function(DemoUser $user_obj) {
                            return $user_obj->getPhoto() ? '/files/images/users/' . $user_obj->getPhoto() : '';
                        },
                        $demo_user_obj->getId(),
                        CRUDFormWidgetUpload::FILE_TYPE_IMAGE
                    )
                )
            ]
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редактирование профиля');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/'),
            new BreadcrumbItemDTO('Пользователи', $this->pathFor(DemoUserListHandler::class)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.main'), $layout_dto);
    }
}
