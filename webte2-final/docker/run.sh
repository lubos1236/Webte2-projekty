#!/bin/sh

if [ ! -e './first_start' ]; then
    sleep 15
    touch ./first_start
    php artisan migrate --force
    php artisan db:seed --force
fi

/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf