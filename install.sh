#!/bin/bash

cp .env.example .env

composer install

./vessel start

./vessel artisan migrate --step
