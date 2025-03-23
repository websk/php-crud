<?php

namespace WebSK\CRUD\Demo\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Demo\DemoCompany;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetReferenceSelect;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class DemoCompanyListAjaxHandler
 * @package WebSK\CRUD\Demo\RequestHandlers
 */
class DemoCompanyListAjaxHandler extends BaseHandler
{

    const string FILTER_NAME = 'demo_company_name';

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $crud_table_obj = $this->crud_service->createTable(
            DemoCompany::class,
            null,
            [
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetReferenceSelect(DemoCompany::_NAME)
                ),
                new CRUDTableColumn(
                    'Наименование компании',
                    new CRUDTableWidgetText(DemoCompany::_NAME)
                )
            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_NAME, 'Название', DemoCompany::_NAME)
            ],
            '',
            'demo_company_list_ajax',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $response->getBody()->write($crud_table_obj->html($request));

        return $response;
    }
}
