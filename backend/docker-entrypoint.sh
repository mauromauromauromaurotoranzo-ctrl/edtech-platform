#!/bin/bash
set -e

echo "🚀 Iniciando EdTech Platform..."

# Crear directorios si no existen
mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs

# Crear archivos de rutas si no existen
if [ ! -f "routes/web.php" ]; then
    echo "<?php
use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return response()->json(['message' => 'EdTech API', 'status' => 'running']);
});" > routes/web.php
fi

if [ ! -f "routes/api.php" ]; then
    echo "<?php
use Illuminate\Support\Facades\Route;
Route::prefix('v1')->group(function () {
    Route::get('/health', fn() => ['status' => 'ok']);
});" > routes/api.php
fi

# Permisos
chown -R www-data:www-data /var/www
chmod -R 755 /var/www/bootstrap/cache
chmod -R 755 /var/www/storage

# Generar autoloader
echo "📦 Generando autoloader..."
composer dump-autoload --optimize || composer dump-autoload

echo "✅ Listo!"

# Ejecutar comando original
exec "$@"
