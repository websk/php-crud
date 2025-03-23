<?php

namespace WebSK\CRUD\Demo\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Demo\DemoCompany;
use WebSK\CRUD\Demo\DemoCompanyService;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTimestamp;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class DemoCompanyEditHandler
 * @package WebSK\CRUD\Demo\RequestHandlers
 */
class DemoCompanyEditHandler extends BaseHandler
{
    /** @Inject */
    protected CRUD $crud_service;

    /** @Inject */
    protected DemoCompanyService $demo_company_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $demo_company_id
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $demo_company_id): ResponseInterface
    {
        $demo_company_obj = $this->demo_company_service->getById($demo_company_id);

        if (!$demo_company_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $crud_form = $this->crud_service->createForm(
            'demo_company_edit',
            $demo_company_obj,
            [
                new CRUDFormRow('Имя на сайте', new CRUDFormWidgetInput(DemoCompany::_NAME)),
                new CRUDFormRow('Создано', new CRUDFormWidgetTimestamp(DemoCompany::_CREATED_AT_TS))
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
            new BreadcrumbItemDTO('Компании', $this->urlFor(DemoCompanyListHandler::class)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.main'), $layout_dto);
    }
}
