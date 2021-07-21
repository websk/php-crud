<?php

namespace WebSK\CRUD\Demo\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Demo\DemoCompany;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLike;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTimestamp;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Slim\Router;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class DemoCompanyListHandler
 * @package WebSK\CRUD\Demo\RequestHandlers
 */
class DemoCompanyListHandler extends BaseHandler
{

    const FILTER_NAME = 'demo_company_name';

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            DemoCompany::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'demo_company_create',
                new DemoCompany(),
                [
                    new CRUDFormRow('Название компании', new CRUDFormWidgetInput(DemoCompany::_NAME)),
                ],
                function(DemoCompany $demo_company_obj) {
                    return Router::pathFor(DemoCompanyEditHandler::class, ['demo_company_id' => $demo_company_obj->getId()]);
                }
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(DemoCompany::_ID)),
                new CRUDTableColumn(
                    'Имя',
                    new CRUDTableWidgetTextWithLink(
                        DemoCompany::_NAME,
                        function(DemoCompany $demo_company_obj) {
                            return Router::pathFor(DemoCompanyEditHandler::class, ['demo_company_id' => $demo_company_obj->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Создано',
                    new CRUDTableWidgetTimestamp(DemoCompany::_CREATED_AT_TS)
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterLike(self::FILTER_NAME, 'Имя на сайте', DemoCompany::_NAME)
            ],
            DemoCompany::_CREATED_AT_TS . ' DESC',
            'demo_company_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Компании');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/'),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.main'), $layout_dto);
    }
}
