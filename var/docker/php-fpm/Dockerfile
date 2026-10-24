ARG PHP_VERSION=8.5
FROM laradock/php-fpm:latest-${PHP_VERSION}@sha256:6e2390fd2a6f811367c34d27ab0021de92eb39ece55eeeda60c8d7e85c38c292

ARG INSTALL_XDEBUG=false
ARG INSTALL_MEMCACHED=false
ARG INSTALL_INTL=false
ARG INSTALL_PCNTL=false

RUN set -eux; \
    apt-get update -yqq; \
    apt-get install -yqq --no-install-recommends \
        ca-certificates \
        curl \
        g++ \
        libicu-dev \
        libmemcached-dev \
        libsasl2-dev \
        libzip-dev \
        pkg-config \
        unzip \
        zip \
        zlib1g-dev; \
    docker-php-ext-configure zip; \
    docker-php-ext-install -j"$(nproc)" zip mysqli; \
    if [ "${INSTALL_INTL}" = "true" ]; then \
        docker-php-ext-configure intl; \
        docker-php-ext-install -j"$(nproc)" intl; \
    fi; \
    if [ "${INSTALL_PCNTL}" = "true" ]; then \
        docker-php-ext-install -j"$(nproc)" pcntl; \
    fi; \
    install_pecl_extension() { \
        extension="$1"; \
        attempt=1; \
        while [ "${attempt}" -le 3 ]; do \
            if pecl install "${extension}"; then \
                return 0; \
            fi; \
            attempt=$((attempt + 1)); \
            sleep 5; \
        done; \
        return 1; \
    }; \
    if [ "${INSTALL_XDEBUG}" = "true" ]; then \
        install_pecl_extension xdebug; \
        docker-php-ext-enable xdebug; \
    fi; \
    if [ "${INSTALL_MEMCACHED}" = "true" ]; then \
        install_pecl_extension memcached; \
        docker-php-ext-enable memcached; \
    fi; \
    rm -rf /tmp/pear ~/.pearrc /var/lib/apt/lists/*; \
    php -m | grep -Fxq zip; \
    php -m | grep -Fxq mysqli; \
    if [ "${INSTALL_INTL}" = "true" ]; then php -m | grep -Fxq intl; fi; \
    if [ "${INSTALL_PCNTL}" = "true" ]; then php -m | grep -Fxq pcntl; fi; \
    if [ "${INSTALL_XDEBUG}" = "true" ]; then php -m | grep -Fxiq xdebug; fi; \
    if [ "${INSTALL_MEMCACHED}" = "true" ]; then php -m | grep -Fxq memcached; fi

RUN set -eux; \
    expected_checksum="$(curl -fsSL https://composer.github.io/installer.sig)"; \
    curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php; \
    actual_checksum="$(php -r "echo hash_file('sha384', '/tmp/composer-setup.php');")"; \
    [ "${expected_checksum}" = "${actual_checksum}" ]; \
    php /tmp/composer-setup.php --quiet --install-dir=/usr/local/bin --filename=composer; \
    rm /tmp/composer-setup.php; \
    composer --version

COPY ./xdebug.ini /tmp/xdebug.ini
RUN set -eux; \
    if [ "${INSTALL_XDEBUG}" = "true" ]; then \
        mv /tmp/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini; \
    else \
        rm /tmp/xdebug.ini; \
    fi

RUN php -v | head -n 1 | grep -Eq '^PHP 8\.5\.'

COPY ./php.ini /usr/local/etc/php/conf.d/99-project.ini
COPY ./phpfpm.pool.conf /usr/local/etc/php-fpm.d/zz-project.conf

RUN usermod -u 1000 www-data

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
