# php-crud

## Install

https://packagist.org/packages/websk/php-crud

install dependency using Composer

```shell
composer require websk/php-crud
```

## Config
* CRUD no special configs

## Demo

* Установить mkcert, https://github.com/FiloSottile/mkcert

* Выполнить:
  ```shell
  mkcert --install
  ```

* Сделать самоподписанный сертификат для `php-crud.devbox`:

  ```shell
  mkcert php-crud.devbox
  ```

* Скопировать полученные файлы _wildcard.php-crud.devbox.pem и _wildcard.php-crud.devbox.pem в `var/docker/nginx/sites`

* Прописать в `/etc/hosts` или аналог в Windows `%WINDIR%\System32\drivers\etc\hosts`

    ```
    127.0.0.1 php-crud.devbox
    ```

* Создаем локальный конфиг, при необходимости вносим изменения:

  ```shell
  cp config/config.example.php config/config.php
  ```

* Заходим в директорию с docker-compose:

  ```shell
  cd var/docker
  ```

* Создаем локальный env файл, при необходимости вносим изменения:

  ```shell
  cp .example.env .env
  ```

* Собираем и запускаем докер-контейнеры:

  ```shell
  docker compose up -d --build
  ```

* Устанавливаем зависимости для проекта

  ```shell
  docker compose exec php-fpm composer install
  ```

* Выполняем миграции БД

  ```shell
  docker compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_auto
  ```
  
  or run handle process migration:

  ```shell
  docker compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_handle
  ```

* Install static

  ```shell
  npm install
  npm run build
  ```

* open `https://php-crud.devbox`
