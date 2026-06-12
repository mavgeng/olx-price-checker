FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
        bash \
        git \
        unzip \
        curl \
        libzip-dev \
        oniguruma-dev \
        mysql-client \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath \
    && apk add --no-cache $PHPIZE_DEPS linux-headers \
    && pecl channel-update pecl.php.net \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ARG HOST_USER_ID=1000
ARG HOST_GROUP_ID=1000
RUN apk --no-cache add shadow && \
    groupmod -o -g ${HOST_GROUP_ID} www-data && \
    usermod -o -u ${HOST_USER_ID} -g www-data www-data

WORKDIR /var/www/laravel

CMD ["php-fpm"]