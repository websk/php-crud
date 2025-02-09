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
    const string DEMO_DB_SERVICE_CONTAINER_ID = 'crud.demo_db_service';
    const string DEMO_DB_ID = 'db_demo_crud';

    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return DBService
         */
        $container->set(self::DEMO_DB_SERVICE_CONTAINER_ID, function (ContainerInterface $container): DBService {
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
        });

        /**
         * @param ContainerInterface $container
         * @return DemoUserService
         */
        $container->set(DemoUserService::class, function (ContainerInterface $container): DemoUserService {
            return new DemoUserService(
                DemoUser::class,
                $container->get(DemoUserRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return DemoUserRepository
         */
        $container->set(DemoUserRepository::class, function (ContainerInterface $container): DemoUserRepository {
            return new DemoUserRepository(
                DemoUser::class,
                $container->get(self::DEMO_DB_SERVICE_CONTAINER_ID)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return DemoCompanyService
         */
        $container->set(DemoCompanyService::class, function (ContainerInterface $container): DemoCompanyService {
            return new DemoCompanyService(
                DemoCompany::class,
                $container->get(DemoCompanyRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return DemoCompanyRepository
         */
        $container->set(DemoCompanyRepository::class, function (ContainerInterface $container): DemoCompanyRepository {
            return new DemoCompanyRepository(
                DemoCompany::class,
                $container->get(self::DEMO_DB_SERVICE_CONTAINER_ID)
            );
        });
    }

    /**
     * @param ContainerInterface $container
     * @return DBService
     */
    public static function getDemoDBService(ContainerInterface $container): DBService
    {
        return $container->get(self::DEMO_DB_SERVICE_CONTAINER_ID);
    }
}
