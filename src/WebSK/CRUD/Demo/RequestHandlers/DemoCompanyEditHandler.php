<?php

namespace WebSK\CRUD\Demo\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Demo\CRUDDemoServiceProvider;
use WebSK\CRUD\Demo\DemoCompany;
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
    /**
     * @param Request $request
     * @param Response $response
     * @param int|null $demo_company_id
     * @return \Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, ?int $demo_company_id = null)
    {
        $demo_company_service = CRUDDemoServiceProvider::getDemoCompanyService($this->container);
        $demo_company_obj = $demo_company_service->getById($demo_company_id);

        if (!$demo_company_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        $crud_form = CRUDServiceProvider::getCrud($this->container)->createForm(
            'demo_company_edit',
            $demo_company_obj,
            [
                new CRUDFormRow('Имя на сайте', new CRUDFormWidgetInput(DemoCompany::_NAME)),
                new CRUDFormRow('Создано', new CRUDFormWidgetTimestamp(DemoCompany::_CREATED_AT_TS))
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
            new BreadcrumbItemDTO('Компании', $this->pathFor(DemoCompanyListHandler::class)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.main'), $layout_dto);
    }
}
