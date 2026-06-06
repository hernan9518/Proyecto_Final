FROM php:8.2-apache

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN sed -i 's/^#\(.*mpm_prefork.*\)/\1/' /etc/apache2/mods-enabled/*.load 2>/dev/null || true
RUN find /etc/apache2/mods-enabled/ -name "mpm_*.load" -delete \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

RUN a2enmod rewrite

COPY . /var/www/html/

RUN mkdir -p /var/www/html/uploads/reportes \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 755 /var/www/html/uploads

EXPOSE 80
