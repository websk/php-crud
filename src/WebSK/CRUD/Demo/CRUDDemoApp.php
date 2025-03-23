<?php

namespace WebSK\CRUD\Demo;

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Psr7\Factory\ResponseFactory;
use WebSK\Cache\CacheServiceProvider;
use WebSK\CRUD\CRUDServiceProvider;

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

        $this->registerRouterSettings($container);

        CacheServiceProvider::register($container);
        CRUDServiceProvider::register($container);
        CRUDDemoServiceProvider::register($container);

        $this->registerRoutes();

        $error_middleware = $this->addErrorMiddleware(true, true, true);
        $error_middleware->setDefaultErrorHandler(ErrorHandler::class);
    }

    /**
     * @param ContainerInterface $container
     */
    protected function registerRouterSettings(ContainerInterface $container): void
    {
        $route_collector = $this->getRouteCollector();
        $route_collector->setDefaultInvocationStrategy($container->get(InvocationStrategyInterface::class));
        $route_parser = $route_collector->getRouteParser();

        $container->set(RouteParserInterface::class, $route_parser);
    }


    protected function registerRoutes(): void
    {
        CRUDDemoRoutes::register($this);
    }
}
