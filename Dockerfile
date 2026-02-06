FROM php:7.4-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli

COPY . /var/www/html/
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

EXPOSE 80
