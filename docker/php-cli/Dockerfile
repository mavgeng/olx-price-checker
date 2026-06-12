FROM php:8.4-cli-alpine

RUN apk add --no-cache \
        gdb \
        strace \
        tzdata \
        mysql-client \
        bash \
        curl \
        bind-tools \
        git \
        openssh-client \
        unzip \
        libzip-dev \
        oniguruma-dev \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath pcntl \
    && pecl install redis pcov \
    && docker-php-ext-enable redis pcov \
    && apk del .build-deps \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ARG HOST_USER_ID=1000
ARG HOST_GROUP_ID=1000
RUN apk --no-cache add shadow && \
    groupmod -o -g ${HOST_GROUP_ID} www-data && \
    usermod -o -u ${HOST_USER_ID} -g www-data www-data

WORKDIR /var/www/laravel