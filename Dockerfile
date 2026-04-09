FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . /var/www/html
COPY docker/start-apache.sh /usr/local/bin/start-apache

RUN chmod +x /usr/local/bin/start-apache \
    && chown -R www-data:www-data /var/www/html

CMD ["start-apache"]
