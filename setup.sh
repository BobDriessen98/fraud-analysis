#!/bin/bash

set -e

echo "Copying environment file"
mv .env.example .env

echo "Starting Docker containers"
docker compose up -d --build

echo "Installing PHP dependencies with Composer"
docker compose exec app composer install

echo "Generating application key"
docker compose exec app php artisan key:generate

echo "Running migrations"
docker compose exec app php artisan migrate --force

echo "Changing file permissions"
sudo chmod -R 777 .

echo "Seeding database"
docker compose exec app php artisan db:seed

echo "Setup complete"
