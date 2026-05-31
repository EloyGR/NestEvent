#!/bin/bash
# Espera a que la base de datos esté disponible antes de continuar usando DATABASE_URL
until php -r '
$url = getenv("DATABASE_URL");
if (!$url) {
  echo "DATABASE_URL not set\n";
  exit(1);
}
$parts = parse_url($url);
if (!$parts) {
  echo "Invalid DATABASE_URL\n";
  exit(1);
}
$host = $parts["host"];
$port = isset($parts["port"]) ? $parts["port"] : 5432;
$db = ltrim($parts["path"], "/");
$user = $parts["user"];
$pass = $parts["pass"];
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