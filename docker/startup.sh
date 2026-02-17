#!/bin/bash
set -e

# =============================================================================
# GovKloud Container Startup Script
# Runs before supervisor starts all services
# =============================================================================

# Prepare Redis data directory (avoid stale RDB on Azure /home volume)
mkdir -p /tmp/redis
rm -f /tmp/redis/dump.rdb

# Write kubeconfig from env var (base64 encoded)
if [ -n "$KUBE_CONFIG_BASE64" ]; then
    mkdir -p /root/.kube
    echo "$KUBE_CONFIG_BASE64" | base64 -d > /root/.kube/config
    chmod 600 /root/.kube/config
    export KUBECONFIG=/root/.kube/config
    echo "[startup] Kubeconfig written to /root/.kube/config"
fi

# Also write kubeconfig for www-data user (PHP-FPM runs as www-data)
if [ -n "$KUBE_CONFIG_BASE64" ]; then
    mkdir -p /home/www-data/.kube
    echo "$KUBE_CONFIG_BASE64" | base64 -d > /home/www-data/.kube/config
    chmod 600 /home/www-data/.kube/config
    chown -R www-data:www-data /home/www-data/.kube
    echo "[startup] Kubeconfig written for www-data user"
fi

# Add helm vcluster repo
helm repo add vcluster https://charts.loft.sh 2>/dev/null || true
helm repo update 2>/dev/null || true
echo "[startup] Helm repos configured"

# Fix storage permissions at runtime (Azure may mount /home with different ownership)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run Laravel setup (clear stale build-time caches, then re-cache with runtime env)
cd /var/www/html
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
# Note: route:cache is skipped because Livewire uses closure-based routes
# that are incompatible with route caching
php artisan view:cache
php artisan storage:link 2>/dev/null || true
php artisan filament:optimize 2>/dev/null || true
php artisan migrate --force || echo "[startup] Migration failed"
echo "[startup] Laravel cache warmed"

# Start supervisor (nginx + php-fpm + redis + queue worker)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
