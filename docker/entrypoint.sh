#!/usr/bin/env bash

composer install -n
bin/console cache:clear
bin/console doctrine:migrations:migrate --no-interaction
bin/console importmap:install
bin/console asset-map:compile
 
exec "$@"