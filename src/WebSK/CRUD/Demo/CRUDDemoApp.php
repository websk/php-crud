<?php

namespace WebSK\CRUD\Demo;

use DI\Container;
use Slim\App;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Psr7\Factory\ResponseFactory;
use WebSK\Cache\CacheServiceProvider;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\MutableContainerAdapter;

/** @extends App<Container> */
class CRUDDemoApp extends App
{
    /**
     * CRUDDemoApp constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct(new ResponseFactory(), $container);

        $this->registerRouterSettings($container);

        $mutable_container = new MutableContainerAdapter($container);
        CacheServiceProvider::register($mutable_container);
        CRUDServiceProvider::register($mutable_container);
        CRUDDemoServiceProvider::register($mutable_container);

        $this->registerRoutes();

        $error_middleware = $this->addErrorMiddleware(true, true, true);
        $error_middleware->setDefaultErrorHandler(ErrorHandler::class);
    }

    /**
     * @param Container $container
     */
    protected function registerRouterSettings(Container $container): void
    {
        $route_collector = $this->getRouteCollector();
        $invocation_strategy = $container->get(InvocationStrategyInterface::class);
        if (!$invocation_strategy instanceof InvocationStrategyInterface) {
            throw new \UnexpectedValueException('Container invocation strategy has an invalid type.');
        }
        $route_collector->setDefaultInvocationStrategy($invocation_strategy);
        $route_parser = $route_collector->getRouteParser();

        $container->set(RouteParserInterface::class, $route_parser);
    }


    protected function registerRoutes(): void
    {
        CRUDDemoRoutes::register($this);
    }
}
