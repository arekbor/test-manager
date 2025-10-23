#!/bin/bash
set -e

echo "Start entrypoint"

mkdir /var/log/test-manager && chown -R www-data:www-data /var/log/test-manager
mkdir -p /var/files/videos
mkdir -p /var/files/testResults
chown -R www-data:www-data /var/files
 
bin/console cache:clear
bin/console app:init-app
bin/console importmap:install
bin/console asset-map:compile

#this solves the permissions problem after asset-map compilation
#https://symfony.com/doc/current/setup/file_permissions.html
setfacl -dR -m u:www-data:rwX -m u:$(whoami):rwX var
setfacl -R -m u:www-data:rwX -m u:$(whoami):rwX var

supervisord -c /etc/supervisor/supervisord.conf

exec apache2-foreground