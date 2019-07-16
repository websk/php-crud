<?php

namespace WebSK\CRUD\Demo;

use Slim\App;
use WebSK\CRUD\Demo\RequestHandlers\DemoUserEditHandler;
use WebSK\CRUD\Demo\RequestHandlers\DemoUserListHandler;
use WebSK\Utils\HTTP;

/**
 * Class CRUDDemoRoutes
 * @package WebSK\CRUD
 */
class CRUDDemoRoutes
{
    const ROUTE_NAME_USER_LIST = 'crud:users:list';
    const ROUTE_NAME_USER_EDIT = 'crud:users:edit';

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/users', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', DemoUserListHandler::class)
                ->setName(self::ROUTE_NAME_USER_LIST);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST],'/{user_id:\d+}', DemoUserEditHandler::class)
                ->setName(self::ROUTE_NAME_USER_EDIT);
        });
    }
}
