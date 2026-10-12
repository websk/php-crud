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

    const string SETTINGS_CONTAINER_ID = 'settings';
    const string PARAM_DB = 'db';

    /**
     * @param ContainerInterface&\ArrayAccess<string, mixed> $container
     */
    public static function register(ContainerInterface&\ArrayAccess $container): void
    {
        /**
         * @param ContainerInterface $container
         * @return DBService
         */
        $container[self::DEMO_DB_SERVICE_CONTAINER_ID] = static function (ContainerInterface $container): DBService {
            $db_config = $container->get(
                self::SETTINGS_CONTAINER_ID . '.' . self::PARAM_DB . '.' . self::DEMO_DB_ID
            );
            if (!is_array($db_config)) {
                throw new \UnexpectedValueException('Demo database configuration must be an array.');
            }

            $host = self::configString($db_config, 'host');
            $db_name = self::configString($db_config, 'db_name');
            $user = self::configString($db_config, 'user');
            $password = self::configString($db_config, 'password');

            $db_connector = new DBConnectorMySQL(
                $host,
                $db_name,
                $user,
                $password
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
        $container[DemoUserService::class] = static function (ContainerInterface $container): DemoUserService {
            return new DemoUserService(
                DemoUser::class,
                self::containerService($container, DemoUserRepository::class, DemoUserRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return DemoUserRepository
         */
        $container[DemoUserRepository::class] = static function (ContainerInterface $container): DemoUserRepository {
            return new DemoUserRepository(
                DemoUser::class,
                self::containerService($container, self::DEMO_DB_SERVICE_CONTAINER_ID, DBService::class)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return DemoCompanyService
         */
        $container[DemoCompanyService::class] = static function (ContainerInterface $container): DemoCompanyService {
            return new DemoCompanyService(
                DemoCompany::class,
                self::containerService($container, DemoCompanyRepository::class, DemoCompanyRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return DemoCompanyRepository
         */
        $container[DemoCompanyRepository::class] = static function (ContainerInterface $container): DemoCompanyRepository {
            return new DemoCompanyRepository(
                DemoCompany::class,
                self::containerService($container, self::DEMO_DB_SERVICE_CONTAINER_ID, DBService::class)
            );
        };
    }

    /** @param array<mixed> $config */
    private static function configString(array $config, string $key): string
    {
        $value = $config[$key] ?? null;
        if (!is_string($value)) {
            throw new \UnexpectedValueException('Database configuration "' . $key . '" must be a string.');
        }

        return $value;
    }

    /**
     * @template T of object
     * @param class-string<T> $expected_class
     * @return T
     */
    private static function containerService(
        ContainerInterface $container,
        string $id,
        string $expected_class
    ): object {
        $service = $container->get($id);
        if (!$service instanceof $expected_class) {
            throw new \UnexpectedValueException('Container service "' . $id . '" has an invalid type.');
        }

        return $service;
    }
}
