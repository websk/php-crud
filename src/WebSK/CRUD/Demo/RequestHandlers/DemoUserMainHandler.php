<?php

namespace WebSK\CRUD\Demo\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $content_html = '<div class="list-group">';
        $content_html .= '<a href="' . $this->urlFor(DemoUserListHandler::class) . '" class="list-group-item">Пользователи</a>';
        $content_html .= '<a href="' . $this->urlFor(DemoCompanyListHandler::class) . '" class="list-group-item">Компании</a>';
        $content_html .= '<div>';

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Компании');
        $layout_dto->setContentHtml($content_html);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.main'), $layout_dto);
    }
}
