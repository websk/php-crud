<?php

namespace WebSK\CRUD\Demo;

use Slim\App;
use Slim\Handlers\Strategies\RequestResponseArgs;
use WebSK\Cache\CacheServiceProvider;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\DB\DBWrapper;
use WebSK\Slim\Facade;

/**
 * Class CRUDApp
 * @package WebSK\CRUD
 */
class CRUDDemoApp extends App
{
    /**
     * CRUDDemoApp constructor.
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

        /** Use facade */
        Facade::setFacadeApplication($this);

        /** Set DBWrapper db service */
        DBWrapper::setDbService(CRUDDemoServiceProvider::getDemoDBService($container));
    }
}
