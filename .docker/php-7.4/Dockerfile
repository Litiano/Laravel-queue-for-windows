FROM php:7.4-cli

RUN apt update
RUN apt install -y libzip-dev unzip git zip

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 -d /home/www-data www-data && groupmod -g 1000 www-data

USER www-data

WORKDIR /home/www-data
