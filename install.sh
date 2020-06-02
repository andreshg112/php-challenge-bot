#!/bin/bash

composer install

cp .env.example .env

./vessel artisan key:generate

./vessel start

./vessel artisan migrate --step
