<?php

use Jgut\Slim\PHPDI\Configuration;
use Jgut\Slim\PHPDI\ContainerBuilder;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\ServerRequestCreatorFactory;
use WebSK\Config\ConfWrapper;
use WebSK\Slim\Facade;

require '../vendor/autoload.php';

$config_path = realpath(__DIR__ . '/../config/config.php');
$config = require_once $config_path;

$configuration = new Configuration();
$configuration->setDefinitions([$config]);
$configuration->setUseAnnotations(true);

$container = ContainerBuilder::build($configuration);
$container->set(ServerRequestInterface::class, function () {
    return ServerRequestCreatorFactory::create()->createServerRequestFromGlobals();
});
ConfWrapper::setConfig($config['settings']);

$app = new \WebSK\CRUD\Demo\CRUDDemoApp($container);

Facade::setFacadeApplication($app);

$app->run();
