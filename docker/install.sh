#!/bin/bash

echo "Generating an application key..."
sail artisan key:generate

echo "Running migrations..."
sail artisan migrate

echo "Running seeders..."
sail artisan db:seed

echo "Creating local development user..."
sail artisan wrmd:create-local-dev-user

echo "Clearing cache..."
sail artisan cache:clear
sail artisan config:clear
sail artisan route:clear
sail artisan view:clear

echo "Linking storage..."
sail artisan storage:link

echo "Import common names into Meilisearch..."
sail artisan scout:import "App\Models\CommonName"

echo "All done :)"
