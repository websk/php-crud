<?php

namespace WebSK\CRUD\Demo\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Config\ConfWrapper;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class DemoUserMainHandler
 * @package WebSK\CRUD\Demo\RequestHandlers
 */
class DemoUserMainHandler extends BaseHandler
{

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $content_html = '<div class="list-group">';
        $content_html .= '<a href="' . $this->pathFor(DemoUserListHandler::class) . '" class="list-group-item">Пользователи</a>';
        $content_html .= '<a href="' . $this->pathFor(DemoCompanyListHandler::class) . '" class="list-group-item">Компании</a>';
        $content_html .= '<div>';

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Компании');
        $layout_dto->setContentHtml($content_html);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.main'), $layout_dto);
    }
}
