<?php

namespace WebSK\CRUD\Demo\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Demo\DemoCompany;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterInVisible;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class DemoCompanyJsonHandler
 * @package WebSK\CRUD\Demo\RequestHandlers
 */
class DemoCompanyJsonHandler extends BaseHandler
{
    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $crud_table_json_obj = $this->crud_service->createTableJSON(
            DemoCompany::class,
            [
                new CRUDTableColumn(
                    DemoCompany::_ID,
                    new CRUDTableWidgetText(DemoCompany::_ID),
                    DemoCompany::_ID
                ),
                new CRUDTableColumn(
                    DemoCompany::_NAME,
                    new CRUDTableWidgetText(DemoCompany::_NAME),
                    DemoCompany::_NAME
                )
            ],
            [
                new CRUDTableFilterInVisible(DemoCompany::_ID, '', DemoCompany::_ID),
                new CRUDTableFilterLikeInline(DemoCompany::_NAME, '', DemoCompany::_NAME)
            ]
        );

        $response->getBody()->write($crud_table_json_obj->json($request));

        return $response;
    }
}
