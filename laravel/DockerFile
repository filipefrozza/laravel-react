FROM php:7.4-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . .

RUN composer install

EXPOSE 8000

ENTRYPOINT ["./run.sh"]