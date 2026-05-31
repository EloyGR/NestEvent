#!/bin/bash

# Espera a que la base de datos esté disponible antes de continuar

until php -r '
try {
    new PDO("pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}", "${DB_USERNAME}", "${DB_PASSWORD}");
    echo "DB is up\n";
    exit(0);
} catch (Exception $e) {
    echo "Waiting for DB... Error: " . $e->getMessage() . "\n";
    exit(1);
}
'; do
  sleep 3
done
