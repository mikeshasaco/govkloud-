# GovKloud Staging Deployment Walkthrough

Complete step-by-step guide for deploying the Laravel application to Azure App Service staging slot.

---

## Prerequisites

- Azure CLI installed and authenticated
- GitHub repo with Laravel codebase
- Azure resources: App Service (`govkloud-app`), MySQL (`govkloud-db`), Resource Group (`govkloud-rg`)

---

## Step 1: Create Staging Deployment Slot

```bash
az webapp deployment slot create \
  --resource-group govkloud-rg \
  --name govkloud-app \
  --slot staging
```

---

## Step 2: Set PHP Version to 8.4

The `composer.lock` requires PHP ≥ 8.4. Azure defaults to 8.2, which causes a fatal Composer platform check error.

```bash
az webapp config set \
  --resource-group govkloud-rg \
  --name govkloud-app \
  --slot staging \
  --linux-fx-version "PHP|8.4"
```

---

## Step 3: Deploy Code via GitHub Actions

Workflow file: `.github/workflows/azure-deploy.yml`

```yaml
name: Deploy to Azure App Service

on:
  push:
    branches: [main, master]

jobs:
  build-and-deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'

      - name: Install Dependencies
        run: composer install --no-dev --optimize-autoloader --no-interaction

      - name: Deploy to Azure
        uses: azure/webapps-deploy@v2
        with:
          app-name: govkloud-app
          slot-name: staging
          publish-profile: ${{ secrets.AZURE_PUBLISH_PROFILE }}
```

### Add GitHub Secret
1. Get publish profile: `az webapp deployment list-publishing-profiles --resource-group govkloud-rg --name govkloud-app --xml`
2. GitHub repo → **Settings** → **Secrets** → **Actions** → New secret
3. Name: `AZURE_PUBLISH_PROFILE`, Value: paste XML
4. Push to trigger: `git push origin master`

---

## Step 4: Create Startup Script

Azure's default nginx config doesn't serve from `/public` or route through Laravel. Create `/home/startup.sh` via SSH:

```bash
az webapp ssh --resource-group govkloud-rg --name govkloud-app --slot staging
```

```bash
cat > /home/startup.sh << 'EOF'
#!/bin/bash

# Create Laravel storage directories
mkdir -p /home/site/wwwroot/storage/framework/{cache/data,sessions,views}
mkdir -p /home/site/wwwroot/storage/logs
mkdir -p /home/site/wwwroot/bootstrap/cache
chmod -R 777 /home/site/wwwroot/storage /home/site/wwwroot/bootstrap/cache

# Fix nginx: serve from /public
sed -i 's|root /home/site/wwwroot;|root /home/site/wwwroot/public;|g' /etc/nginx/sites-enabled/default

# Fix nginx: Laravel routing (try_files)
sed -i '/location \/ {/,/}/c\
    location / {\
        try_files $uri $uri/ /index.php?$query_string;\
    }' /etc/nginx/sites-enabled/default

# Disable error interception (show real Laravel errors)
sed -i 's/fastcgi_intercept_errors on/fastcgi_intercept_errors off/g' /etc/nginx/sites-enabled/default

# Apply changes
nginx -s reload
EOF

chmod +x /home/startup.sh
exit
```

> [!IMPORTANT]
> Do **NOT** add `php-fpm` at the end of the startup script. Azure handles PHP-FPM startup automatically. Adding it causes a port conflict crash.

Set as startup command:

```bash
az webapp config set \
  --resource-group govkloud-rg \
  --name govkloud-app \
  --slot staging \
  --startup-file "/home/startup.sh"
```

### What the Startup Script Fixes

| Problem | Fix |
|---|---|
| nginx serves from `/home/site/wwwroot` instead of `/public` | `sed` to update `root` directive |
| No Laravel routing (all non-file URLs return 404) | Add `try_files $uri $uri/ /index.php?$query_string` |
| nginx hides PHP errors as generic 404 | Disable `fastcgi_intercept_errors` |
| Missing storage directories (Blade compilation fails) | `mkdir -p` for framework cache/sessions/views |

> [!NOTE]
> The `/home` directory persists across restarts. The nginx config at `/etc/nginx/` does NOT persist — that's why the startup script re-applies fixes on every container start.

---

## Step 5: Configure Environment Variables

Set via **Azure Portal** (CLI sometimes shows `null` values even when set):

1. Portal → `govkloud-app` → **Deployment slots** → **staging**
2. **Configuration** → **Application settings** → **Advanced edit**
3. Paste this JSON:

```json
[
  { "name": "APP_DEBUG", "value": "true", "slotSetting": false },
  { "name": "APP_ENV", "value": "staging", "slotSetting": false },
  { "name": "APP_KEY", "value": "base64:YOUR_APP_KEY_HERE", "slotSetting": false },
  { "name": "APP_NAME", "value": "GovKloud", "slotSetting": false },
  { "name": "APP_URL", "value": "https://govkloud-app-staging.azurewebsites.net", "slotSetting": false },
  { "name": "CACHE_DRIVER", "value": "database", "slotSetting": false },
  { "name": "DB_CONNECTION", "value": "mysql", "slotSetting": false },
  { "name": "DB_DATABASE", "value": "govkloud_staging", "slotSetting": false },
  { "name": "DB_HOST", "value": "govkloud-db.mysql.database.azure.com", "slotSetting": false },
  { "name": "DB_PASSWORD", "value": "YOUR_DB_PASSWORD", "slotSetting": false },
  { "name": "DB_PORT", "value": "3306", "slotSetting": false },
  { "name": "DB_USERNAME", "value": "dbadmin", "slotSetting": false },
  { "name": "LOG_CHANNEL", "value": "stack", "slotSetting": false },
  { "name": "LOG_LEVEL", "value": "debug", "slotSetting": false },
  { "name": "MYSQL_ATTR_SSL_CA", "value": "/etc/ssl/certs/ca-certificates.crt", "slotSetting": false },
  { "name": "QUEUE_CONNECTION", "value": "database", "slotSetting": false },
  { "name": "SESSION_DRIVER", "value": "database", "slotSetting": false },
  { "name": "STRIPE_KEY", "value": "pk_test_...", "slotSetting": false },
  { "name": "STRIPE_SECRET", "value": "sk_test_...", "slotSetting": false },
  { "name": "WEBSITE_DOCUMENT_ROOT", "value": "/public", "slotSetting": false }
]
```

> [!CAUTION]
> `MYSQL_ATTR_SSL_CA` is **required**. Azure MySQL enforces `--require_secure_transport=ON`. Without this setting, all database connections fail with error 3159.

---

## Step 6: Run Database Migrations

```bash
az webapp ssh --resource-group govkloud-rg --name govkloud-app --slot staging
```

```bash
cd /home/site/wwwroot
php artisan migrate --force
```

---

## Step 7: Restart and Verify

```bash
az webapp restart --resource-group govkloud-rg --name govkloud-app --slot staging
```

Visit: **https://govkloud-app-staging.azurewebsites.net**

### Check Logs If Issues

```bash
az webapp log tail --resource-group govkloud-rg --name govkloud-app --slot staging
```

---

## Issues We Hit & Solutions

| Issue | Symptom | Root Cause | Fix |
|---|---|---|---|
| PHP version mismatch | `Composer detected issues: requires PHP >= 8.4.0` | Azure defaulted to PHP 8.2 | Set `--linux-fx-version "PHP\|8.4"` |
| "File not found" | Blank page or nginx "File not found" | nginx root at `/home/site/wwwroot` not `/public` | Startup script with `sed` |
| Redirect loop | `ERR_TOO_MANY_REDIRECTS` | Accidental `index.php` redirect in wwwroot root | Delete `/home/site/wwwroot/index.php` (keep `public/index.php`) |
| nginx 404 for all routes | 404 on every page | Missing `try_files` directive for Laravel routing | Add `try_files $uri $uri/ /index.php?$query_string` |
| Hidden PHP errors | nginx shows 404 but PHP returns 500 | `fastcgi_intercept_errors on` hides real errors | Set to `off` in startup script |
| Blade compilation fail | `Please provide a valid cache path` | Missing `storage/framework/*` directories | `mkdir -p` in startup script |
| MySQL SSL required | `Connections using insecure transport prohibited` | Azure MySQL requires SSL | Set `MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt` |
| Missing tables | `Table 'govkloud_staging.sessions' doesn't exist` | Migrations never run on staging DB | `php artisan migrate --force` |
| App won't start | SSH unreachable after restart | Startup script ran `php-fpm` causing port conflict | Remove `php-fpm` from startup script |

---

## Production Deployment (Future)

When staging is verified:

```bash
az webapp deployment slot swap \
  --resource-group govkloud-rg \
  --name govkloud-app \
  --slot staging \
  --target-slot production
```

> [!WARNING]
> Before swapping, update production app settings separately (especially `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://govkloud.com`).
