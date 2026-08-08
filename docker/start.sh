#!/bin/sh
set -e

php artisan config:clear
php artisan migrate:fresh --seed --force
php artisan tinker --execute="echo 'Products: ' . App\Models\Product::count() . ', Users: ' . App\Models\User::count() . PHP_EOL;"

php artisan reverb:start --host=0.0.0.0 --port=6001 &
php artisan queue:work &
php artisan serve --host=0.0.0.0 --port=8000 &

exec caddy run --config /etc/caddy/Caddyfile --adapter caddyfile
