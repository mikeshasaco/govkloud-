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

# Run Laravel setup
cd /var/www/html
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force 2>/dev/null || echo "[startup] Migration skipped or failed"
echo "[startup] Laravel cache warmed"

# Start supervisor (nginx + php-fpm + redis + queue worker)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
