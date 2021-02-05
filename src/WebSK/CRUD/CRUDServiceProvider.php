<?php

namespace WebSK\CRUD;

use Psr\Container\ContainerInterface;

/**
 * Class CRUDServiceProvider
 * @package WebSK\CRUD
 */
class CRUDServiceProvider
{
    const CRUD_CONTAINER_ID = 'crud';

    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        $container[self::CRUD_CONTAINER_ID] = function (ContainerInterface $container): CRUD {
            return new CRUD($container);
        };
    }

    /**
     * @param ContainerInterface $container
     * @return CRUD
     */
    public static function getCrud(ContainerInterface $container): CRUD
    {
        return $container[self::CRUD_CONTAINER_ID];
    }
}
