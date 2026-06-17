#!/bin/sh
set -e

cd /var/www/html

# Esperar a MySQL
echo "Esperando a MySQL en ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
until php -r "exit(@fsockopen(getenv('DB_HOST') ?: 'mysql', (int)(getenv('DB_PORT') ?: 3306)) ? 0 : 1);" 2>/dev/null; do
    sleep 2
done
echo "MySQL disponible."

# Clave de app si falta
if ! grep -q "^APP_KEY=base64" .env 2>/dev/null; then
    php artisan key:generate --force || true
fi

# Migraciones y datos históricos
php artisan migrate --force || true
php artisan mundial:historico || true
php artisan mundial:sync || true

# Cache de configuración
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Permisos
chown -R www-data:www-data storage bootstrap/cache || true

exec "$@"
