# WebSK php-crud

CRUD forms, tables, filters and widgets for WebSK applications based on Slim 4 and PHP-DI.

## Requirements

- PHP 8.3 or newer, including PHP 8.5
- Composer 2
- PHP extensions: `fileinfo`, `json`, `mbstring`, `pdo`
- Node.js 24 LTS and npm 11 when rebuilding the demo frontend assets

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
npm run check
npm run audit:production
npm run audit:high
```

The build copies an explicit set of browser files from `node_modules` into the ignored
`public/assets/libraries` directory. `npm run check` checks the asset-manifest JavaScript,
verifies every declared source asset, rejects server-side files anywhere under `public/assets`,
compares the local assets with a temporary rebuild, and runs the documented frontend behavior
baseline in a real Chromium browser. Install the Playwright browser once with `npx playwright install chromium`;
set `PLAYWRIGHT_CHANNEL=chrome` to use an installed Google Chrome. `audit:production` fails on any
production advisory of moderate severity or higher; `audit:high` checks the complete dependency
tree for high and critical findings.

With the Docker demo running, use `PLAYWRIGHT_CHANNEL=chrome npm run test:browser:docker`
to exercise the real user list and edit routes, create popup, Ace editor and remote multiple
autocomplete. The check edits controls in the browser without saving entities.

The demo and generated CRUD markup use Bootstrap 5.3.8. Native browser date controls are used by
the CRUD date, date-time and timestamp widgets, so Moment and Bootstrap Datetimepicker are no
longer required. MediumEditor, Blueimp File Upload and Tokenize2 are served from generated local
assets; the demo no longer relies on third-party frontend CDNs.
Shared form, popup and filter rules are generated as `/assets/libraries/crud/crud.css`.
Applications consuming `websk/php-crud` must publish that file together with the npm-managed
library assets and include it after Bootstrap or their Bootstrap-based theme styles.

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
  npm run check
  npm run audit:production
  npm run audit:high
  ```

* Open `https://php-crud.devbox`.

## License

This project is licensed under the [MIT License](LICENSE).
