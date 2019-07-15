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
     * @return CRUD
     */
    public static function getCrud(ContainerInterface $container)
    {
        return $container[self::CRUD_CONTAINER_ID];
    }

    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        $container[self::CRUD_CONTAINER_ID] = function (ContainerInterface $container) {
            return new CRUD($container);
        };
    }
}
