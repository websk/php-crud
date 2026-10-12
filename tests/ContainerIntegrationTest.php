<?php

namespace WebSK\CRUD\Tests;

use Jgut\Slim\PHPDI\Configuration;
use Jgut\Slim\PHPDI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Demo\RequestHandlers\DemoCompanyJsonHandler;
use WebSK\CRUD\MutableContainerAdapter;

final class ContainerIntegrationTest extends TestCase
{
    public function testAdapterRegistersDefinitionsInPhpDiSeven(): void
    {
        $container = ContainerBuilder::build();
        $adapter = new MutableContainerAdapter($container);
        $adapter['answer'] = 42;

        self::assertTrue($adapter->has('answer'));
        self::assertSame(42, $container->get('answer'));
    }

    public function testInjectAttributeResolvesCrudService(): void
    {
        $configuration = new Configuration();
        $configuration->setUseAttributes(true);
        $container = ContainerBuilder::build($configuration);
        CRUDServiceProvider::register(new MutableContainerAdapter($container));

        $handler = $container->get(DemoCompanyJsonHandler::class);
        self::assertInstanceOf(DemoCompanyJsonHandler::class, $handler);
        $property = new \ReflectionProperty($handler, 'crud_service');

        self::assertInstanceOf(CRUD::class, $property->getValue($handler));
    }
}
