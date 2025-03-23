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
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container): void
    {
        $container->set(CRUD::class, function (ContainerInterface $container): CRUD {
            return new CRUD($container);
        });
    }
}
