#!/bin/bash
set -e

echo "Start entrypoint"

composer install -n

su -s /bin/bash www-data -c "bin/console cache:clear"
su -s /bin/bash www-data -c "bin/console doctrine:migrations:migrate --no-interaction"
su -s /bin/bash www-data -c "bin/console app:create-app-settings"
su -s /bin/bash www-data -c "bin/console app:create-admin-user"
su -s /bin/bash www-data -c "bin/console importmap:install"
su -s /bin/bash www-data -c "bin/console asset-map:compile"

#this solves the permissions problem after asset-map compilation
#https://symfony.com/doc/current/setup/file_permissions.html
setfacl -dR -m u:www-data:rwX -m u:$(whoami):rwX var
setfacl -R -m u:www-data:rwX -m u:$(whoami):rwX var

supervisord -c /etc/supervisor/supervisord.conf

exec apache2-foreground