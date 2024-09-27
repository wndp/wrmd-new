#!/bin/bash

echo "Generating an application key..."
sail artisan key:generate

echo "Running migrations..."
sail artisan migrate

echo "Running seeders..."
sail artisan db:seed

echo "Creating local development user..."
sail artisan app:create-test-user

echo "Clearing cache..."
sail artisan cache:clear
sail artisan config:clear
sail artisan route:clear
sail artisan view:clear

echo "Linking storage..."
sail artisan storage:link

echo "All done :)"
