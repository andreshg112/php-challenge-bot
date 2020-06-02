#!/bin/bash

composer install

cp .env.example .env

php artisan key:generate

./vessel start

./vessel artisan migrate --step
