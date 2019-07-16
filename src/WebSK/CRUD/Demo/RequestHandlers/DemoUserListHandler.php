<?php

namespace WebSK\CRUD\Demo\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Demo\CRUDDemoRoutes;
use WebSK\CRUD\Demo\CRUDDemoServiceProvider;
use WebSK\CRUD\Demo\DemoUser;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetHtml;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Slim\Router;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class UserListHandler
 * @package WebSK\CRUD\Demo\RequestHandlers
 */
class DemoUserListHandler extends BaseHandler
{
    const FILTER_EMAIL = 'user_email';
    const FILTER_NAME = 'user_name';

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            DemoUser::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'user_create',
                new DemoUser(),
                [
                    new CRUDFormRow('Имя на сайте', new CRUDFormWidgetInput(DemoUser::_NAME)),
                    new CRUDFormRow('Имя', new CRUDFormWidgetInput(DemoUser::_FIRST_NAME)),
                    new CRUDFormRow('Фамилия', new CRUDFormWidgetInput(DemoUser::_LAST_NAME)),
                    new CRUDFormRow('Email', new CRUDFormWidgetInput(DemoUser::_EMAIL))
                ],
                $this->pathFor(CRUDDemoRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => '{this->' . DemoUser::_ID . '}'])
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(DemoUser::_ID)),
                new CRUDTableColumn(
                    'Имя',
                    new CRUDTableWidgetTextWithLink(
                        DemoUser::_NAME,
                        function(DemoUser $user_obj) {
                            return Router::pathFor(CRUDDemoRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_obj->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Email',
                    new CRUDTableWidgetText(DemoUser::_EMAIL)
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_NAME, 'Имя на сайте', DemoUser::_NAME),
                new CRUDTableFilterLikeInline(self::FILTER_EMAIL, 'Email', DemoUser::_EMAIL),
            ],
            DemoUser::_CREATED_AT_TS . ' DESC',
            'users_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Пользователи');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/'),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.main'), $layout_dto);
    }
}
