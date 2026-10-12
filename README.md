# WebSK php-crud

CRUD forms, tables, filters and widgets for WebSK applications based on Slim 4 and PHP-DI.

## Requirements

- PHP 8.3 or newer, including PHP 8.5
- Composer 2
- PHP extensions: `fileinfo`, `json`, `mbstring`, `pdo`

## Install

https://packagist.org/packages/websk/php-crud

Install the package with Composer:

```shell
composer require websk/php-crud
```

## Config

CRUD requires no package-specific configuration. Register `CRUDServiceProvider` in a mutable PSR-11 container. PHP-DI 7 applications can use `MutableContainerAdapter` while registering legacy WebSK service providers.

## Development

Run all PHP checks with:

```shell
composer check
```

This command runs syntax checks, PHPUnit and PHPStan at level `max`.

Build static demo assets with:

```shell
npm ci
npm run build
```

## Demo

* Install [mkcert](https://github.com/FiloSottile/mkcert).

* Initialize its local CA:
  ```shell
  mkcert --install
  ```

* Create a local certificate for `php-crud.devbox`:

  ```shell
  mkcert php-crud.devbox
  ```

* Copy the generated certificate and key to `var/docker/nginx/sites` using the names referenced by the nginx configuration.

* Add the host to `/etc/hosts`, or `%WINDIR%\System32\drivers\etc\hosts` on Windows:

    ```
    127.0.0.1 php-crud.devbox
    ```

* Create the local application configuration and adjust it if needed:

  ```shell
  cp config/config.example.php config/config.php
  ```

* Enter the Docker directory:

  ```shell
  cd var/docker
  ```

* Create the local environment file and adjust it if needed:

  ```shell
  cp .example.env .env
  ```

* Build and start the containers. The PHP-FPM image uses PHP 8.5:

  ```shell
  docker compose up -d --build
  ```

* Install project dependencies:

  ```shell
  docker compose exec php-fpm composer install
  ```

* Run database migrations:

  ```shell
  docker compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_auto
  ```
  
  Or run migrations interactively:

  ```shell
  docker compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_handle
  ```

* Build static assets:

  ```shell
  npm ci
  npm run build
  ```

* Open `https://php-crud.devbox`.

## License

This project is licensed under the [MIT License](LICENSE).
