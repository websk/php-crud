<?php

namespace WebSK\CRUD;

use Psr\Container\ContainerInterface;

/**
 * Class CRUDServiceProvider
 * @package WebSK\CRUD
 */
class CRUDServiceProvider
{
    /**
     * @param ContainerInterface&\ArrayAccess<array-key, mixed> $container
     */
    public static function register(ContainerInterface&\ArrayAccess $container): void
    {
        $container[CRUD::class] = static function (ContainerInterface $container): CRUD {
            return new CRUD($container);
        };
    }
}
