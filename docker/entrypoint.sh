#!/usr/bin/env bash

composer install -n
bin/console cache:clear
bin/console doctrine:migrations:migrate --no-interaction
bin/console app:create-admin-user
bin/console importmap:install
bin/console asset-map:compile
 
exec "$@"