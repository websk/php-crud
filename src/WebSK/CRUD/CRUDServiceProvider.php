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
    public static function register(ContainerInterface $container)
    {
        $container->set(CRUD::class, function (ContainerInterface $container): CRUD {
            return new CRUD($container);
        });
    }

    /**
     * @param ContainerInterface $container
     * @return CRUD
     */
    public static function getCrud(ContainerInterface $container): CRUD
    {
        return $container->get(CRUD::class);
    }
}
