FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2dismod mpm_event && a2enmod mpm_prefork && a2enmod rewrite

COPY . /var/www/html/

RUN mkdir -p /var/www/html/uploads/reportes \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 755 /var/www/html/uploads

ENV PORT=80
ENV APACHE_PORT=80

EXPOSE 80

CMD ["apache2-foreground"]
