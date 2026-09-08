#!/bin/bash
set -e

echo "Iniciando Jireh POS (Laravel 8)..."

echo "Esperando MySQL en ${DB_HOST}:${DB_PORT}..."
while ! nc -z "${DB_HOST}" "${DB_PORT}" 2>/dev/null; do
    sleep 2
done
echo "MySQL disponible"

# El volumen public_root tapa /app/public. Restaurar assets de código
# sin pisar fotos/comprobantes subidos.
if [ -d /opt/public-dist ]; then
    echo "Sincronizando public/ (excluye assets/imgs y uploads)..."
    mkdir -p \
        /app/public/assets/imgs/clientes \
        /app/public/assets/imgs/vehiculos \
        /app/public/assets/imgs/users \
        /app/public/assets/imgs/logos \
        /app/public/assets/imgs/pagos \
        /app/public/uploads/comprobantes
    rsync -a \
        --exclude 'assets/imgs/' \
        --exclude 'uploads/' \
        /opt/public-dist/ /app/public/
fi

if [ "${JIREH_RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Ejecutando migraciones..."
    php artisan migrate --force
else
    echo "Migraciones omitidas (dump SQL es la fuente de verdad). JIREH_RUN_MIGRATIONS=true para activarlas."
fi

# No regenerar APP_KEY. Solo cachear config/vistas.
php artisan config:cache || true
php artisan view:clear || true

chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    /app/public/assets/imgs /app/public/uploads || true
chmod -R 775 /app/storage /app/bootstrap/cache \
    /app/public/assets/imgs /app/public/uploads || true

echo "Listo. Ejecutando: $*"
exec "$@"
