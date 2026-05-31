#!/bin/bash
# Espera a que la base de datos esté disponible antes de continuar
until php -r '
$host = getenv("DB_HOST");
$port = getenv("DB_PORT");
$db = getenv("DB_DATABASE");
$user = getenv("DB_USERNAME");
$pass = getenv("DB_PASSWORD");
try {
    new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    echo "DB is up\n";
    exit(0);
} catch (Exception $e) {
    echo "Waiting for DB... Error: " . $e->getMessage() . "\n";
    exit(1);
}
'; do
  sleep 3
done