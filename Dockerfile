FROM php:8.3.12-apache

RUN a2enmod rewrite

RUN apt-get update \
  && apt-get install -y libzip-dev git wget libpq-dev acl --no-install-recommends \
  && apt-get install -y supervisor \
  && apt-get install php8.3-gd \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install pdo pdo_pgsql zip

COPY docker/php.ini /usr/local/etc/php/
COPY docker/security.conf /etc/apache2/conf-enabled/security.conf
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/supervisor/messenger-worker.conf /etc/supervisor/conf.d/messenger-worker.conf

COPY docker/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

RUN wget https://getcomposer.org/download/2.7.7/composer.phar \ 
    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer

COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf 
COPY . /var/www

RUN mkdir /home/uploads/ && chmod a+w /home/uploads/

WORKDIR /var/www

ENTRYPOINT ["/entrypoint.sh"]