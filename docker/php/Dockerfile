# See https://hub.docker.com/r/prooph/php
FROM prooph/php:7.4-cli-xdebug

RUN docker-php-ext-install pgsql

ENV COMPOSER_HOME=/var/composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
