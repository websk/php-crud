<?php

namespace WebSK\CRUD\Demo;

use Fig\Http\Message\RequestMethodInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;
use WebSK\CRUD\Demo\RequestHandlers\DemoCompanyEditHandler;
use WebSK\CRUD\Demo\RequestHandlers\DemoCompanyJsonHandler;
use WebSK\CRUD\Demo\RequestHandlers\DemoCompanyListAjaxHandler;
use WebSK\CRUD\Demo\RequestHandlers\DemoCompanyListHandler;
use WebSK\CRUD\Demo\RequestHandlers\DemoUserEditHandler;
use WebSK\CRUD\Demo\RequestHandlers\DemoUserListHandler;
use WebSK\CRUD\Demo\RequestHandlers\DemoUserMainHandler;

/**
 * Class CRUDDemoRoutes
 * @package WebSK\CRUD
 */
class CRUDDemoRoutes
{

    /**
     * @param App $app
     */
    public static function register(App $app): void
    {
        $app->get('/', DemoUserMainHandler::class)->setName(DemoUserMainHandler::class);

        $app->group('/demo_users', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', DemoUserListHandler::class)
                ->setName(DemoUserListHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],'/{demo_user_id:\d+}', DemoUserEditHandler::class)
                ->setName(DemoUserEditHandler::class);
        });


        $app->group('/demo_companies', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', DemoCompanyListHandler::class)
                ->setName(DemoCompanyListHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/ajax', DemoCompanyListAjaxHandler::class)
                ->setName(DemoCompanyListAjaxHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/json', DemoCompanyJsonHandler::class)
                ->setName(DemoCompanyJsonHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/{demo_company_id:\d+}', DemoCompanyEditHandler::class)
                ->setName(DemoCompanyEditHandler::class);
        });
    }
}
