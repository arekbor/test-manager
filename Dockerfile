FROM php:8.4.13-apache

RUN a2enmod rewrite

RUN apt-get update \
  && apt-get install -y libzip-dev git wget libpq-dev acl libjpeg-dev libpng-dev libfreetype6-dev \
  && apt-get install -y supervisor \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install pdo pdo_pgsql zip gd
  

COPY docker/php.ini /usr/local/etc/php/
COPY docker/security.conf /etc/apache2/conf-enabled/security.conf
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/supervisor/messenger-worker.conf /etc/supervisor/conf.d/messenger-worker.conf

COPY docker/entrypoint.sh /entrypoint.sh

RUN chmod u+x /entrypoint.sh

RUN wget https://getcomposer.org/download/2.8.12/composer.phar \ 
    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer

COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf 

COPY . /var/www

RUN chown -R www-data:www-data /var/www

RUN mkdir /home/uploads && chown -R www-data:www-data /home/uploads

RUN mkdir /var/log/test-manager && chown -R www-data:www-data /var/log/test-manager

RUN rm -r /var/www/docker

ENTRYPOINT [ "/entrypoint.sh" ]