#!/bin/bash

php artisan down

git pull

composer update

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
php artisan migrate
php artisan optimize

php artisan up
