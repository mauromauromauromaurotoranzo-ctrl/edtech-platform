#!/bin/bash
set -e

echo "🚀 Iniciando EdTech Platform..."

# Crear directorios si no existen
mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs

# Permisos
chown -R www-data:www-data /var/www
chmod -R 755 /var/www/bootstrap/cache
chmod -R 755 /var/www/storage

# Generar autoloader si es necesario
if [ ! -d "vendor/composer" ]; then
    echo "📦 Instalando dependencias..."
    composer install --no-interaction --optimize-autoloader --ignore-platform-reqs
else
    echo "📦 Optimizando autoloader..."
    composer dump-autoload --optimize || true
fi

# Ejecutar comando original
exec "$@"
