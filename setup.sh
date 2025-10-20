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
echo "You can now start the external API container with:"
echo "  docker run -p 8080:80 vzdeveloper/customers-api:latest"
echo "Then open: http://localhost/scans/index"
