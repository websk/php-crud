<?php

namespace WebSK\CRUD\Demo;

use Slim\App;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Cache\CacheServiceProvider;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\DB\DBWrapper;
use WebSK\Slim\Facade;
use WebSK\Slim\Router;

/**
 * Class CRUDApp
 * @package WebSK\CRUD
 */
class CRUDDemoApp extends App
{
    /**
     * SkifApp constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $container = $this->getContainer();

        CacheServiceProvider::register($container);
        CRUDServiceProvider::register($container);
        CRUDDemoServiceProvider::register($container);

        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        $container = $this->getContainer();
        $container['foundHandler'] = function () {
            return new RequestResponseArgs();
        };

        CRUDDemoRoutes::register($this);

        $this->get('/', function (Request $request, Response $response) {
            return $response->withRedirect(Router::pathFor(CRUDDemoRoutes::ROUTE_NAME_USER_LIST));
        });

        /** Use facade */
        Facade::setFacadeApplication($this);

        /** Set DBWrapper db service */
        DBWrapper::setDbService(CRUDDemoServiceProvider::getDemoDBService($container));
    }
}
