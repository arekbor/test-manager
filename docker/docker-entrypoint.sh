#!/bin/bash
set -e

echo "Start entrypoint"

bin/console cache:clear
bin/console app:init-app
bin/console importmap:install
bin/console asset-map:compile

#this solves the permissions problem after asset-map compilation
#https://symfony.com/doc/current/setup/file_permissions.html
setfacl -dR -m u:www-data:rwX -m u:$(whoami):rwX var
setfacl -R -m u:www-data:rwX -m u:$(whoami):rwX var

mkdir /home/uploads
mkdir /home/uploads/videos
mkidr /home/uploads/testResults
RUN chown -R www-data:www-data /home/uploads

supervisord -c /etc/supervisor/supervisord.conf
exec apache2-foreground