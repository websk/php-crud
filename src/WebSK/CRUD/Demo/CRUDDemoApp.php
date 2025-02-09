<?php

namespace WebSK\CRUD\Demo;

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Handlers\ErrorHandler;
use Slim\Psr7\Factory\ResponseFactory;
use WebSK\Cache\CacheServiceProvider;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\DB\DBWrapper;

/**
 * Class CRUDApp
 * @package WebSK\CRUD
 */
class CRUDDemoApp extends App
{
    /**
     * CRUDDemoApp constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct(new ResponseFactory(), $container);

        $container = $this->getContainer();

        CacheServiceProvider::register($container);
        CRUDServiceProvider::register($container);
        CRUDDemoServiceProvider::register($container);

        $this->registerRoutes();

        $error_middleware = $this->addErrorMiddleware(true, true, true);
        $error_middleware->setDefaultErrorHandler(ErrorHandler::class);

        /** Set DBWrapper db service */
        DBWrapper::setDbService(CRUDDemoServiceProvider::getDemoDBService($container));
    }

    protected function registerRoutes(): void
    {
        CRUDDemoRoutes::register($this);
    }
}
