<?php

namespace WebSK\CRUD\Demo\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDCompiler;
use WebSK\CRUD\Demo\DemoCompany;
use WebSK\CRUD\Demo\DemoUser;
use WebSK\CRUD\Demo\DemoUserService;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetAceTextarea;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetDate;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTimestamp;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetUpload;
use WebSK\Entity\InterfaceEntity;
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
    #[\DI\Attribute\Inject]
    protected DemoUserService $demo_user_service;

    #[\DI\Attribute\Inject]
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $demo_user_id
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $demo_user_id): ResponseInterface
    {
        $demo_user_obj = $this->demo_user_service->getById($demo_user_id);

        $crud_form = $this->crud_service->createForm(
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
                        $this->urlFor(DemoCompanyListAjaxHandler::class),
                        $this->urlFor(
                            DemoCompanyEditHandler::class,
                            ['demo_company_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                        )
                    )
                ),
                new CRUDFormRow(
                    'Комментарий',
                    new CRUDFormWidgetAceTextarea(
                        DemoUser::_COMMENT,
                        CRUDFormWidgetAceTextarea::ACE_MODE_TEXT,
                        false,
                        true
                    )
                ),
                new CRUDFormRow('Создано', new CRUDFormWidgetTimestamp(DemoUser::_CREATED_AT_TS)),
                new CRUDFormRow(
                    'Фото',
                    new CRUDFormWidgetUpload(
                        DemoUser::_PHOTO,
                        'files',
                        'images' . DIRECTORY_SEPARATOR . 'users',
                        function(InterfaceEntity $user_obj): string {
                            if (!$user_obj instanceof DemoUser) {
                                throw new \InvalidArgumentException('Expected ' . DemoUser::class);
                            }
                            return $user_obj->getPhoto() ? '/files/images/users/' . $user_obj->getPhoto() : '';
                        },
                        CRUDCompiler::entityId($demo_user_obj),
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
            new BreadcrumbItemDTO('Пользователи', $this->urlFor(DemoUserListHandler::class)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, CRUDCompiler::string(ConfWrapper::value('layout.main'), 'layout.main'), $layout_dto);
    }
}
