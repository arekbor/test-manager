#!/bin/bash
set -e

echo "Start entrypoint"

bin/console cache:clear
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate --no-interaction
bin/console app:create-app-settings
bin/console app:create-admin-user
bin/console importmap:install
bin/console asset-map:compile

#this solves the permissions problem after asset-map compilation
#https://symfony.com/doc/current/setup/file_permissions.html
setfacl -dR -m u:www-data:rwX -m u:$(whoami):rwX var
setfacl -R -m u:www-data:rwX -m u:$(whoami):rwX var

supervisord -c /etc/supervisor/supervisord.conf
exec apache2-foreground