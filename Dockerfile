FROM php:8.2-fpm

# Включаем необходимые расширения
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli pdo pdo_mysql

# Убедимся что allow_url_fopen включен
RUN echo "allow_url_fopen = On" >> /usr/local/etc/php/conf.d/docker-php.ini

WORKDIR /var/www/html
