<?php

namespace WebSK\CRUD\Demo;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\DB\DBConnectorMySQL;
use WebSK\DB\DBService;
use WebSK\DB\DBSettings;

/**
 * Class CRUDDemoServiceProvider
 * @package WebSK\CRUD\Demo
 */
class CRUDDemoServiceProvider
{
    const DEMO_DB_SERVICE_CONTAINER_ID = 'crud.demo_db_service';
    const DEMO_DB_ID = 'db_demo_crud';

    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return DBService
         */
        $container[self::DEMO_DB_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            $db_config = $container['settings']['db'][self::DEMO_DB_ID];

            $db_connector = new DBConnectorMySQL(
                $db_config['host'],
                $db_config['db_name'],
                $db_config['user'],
                $db_config['password']
            );

            $db_settings = new DBSettings(
                'mysql'
            );

            return new DBService($db_connector, $db_settings);
        };

        /**
         * @param ContainerInterface $container
         * @return DemoUserService
         */
        $container[DemoUser::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new DemoUserService(
                DemoUser::class,
                $container->get(DemoUser::ENTITY_REPOSITORY_CONTAINER_ID),
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return DemoUserRepository
         */
        $container[DemoUser::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new DemoUserRepository(
                DemoUser::class,
                $container->get(self::DEMO_DB_SERVICE_CONTAINER_ID)
            );
        };
    }

    /**
     * @param ContainerInterface $container
     * @return DBService
     */
    public static function getDemoDBService(ContainerInterface $container)
    {
        return $container->get(self::DEMO_DB_SERVICE_CONTAINER_ID);
    }

    /**
     * @param ContainerInterface $container
     * @return DemoUserService
     */
    public static function getDemoUserService(ContainerInterface $container)
    {
        return $container->get(DemoUser::ENTITY_SERVICE_CONTAINER_ID);
    }
}