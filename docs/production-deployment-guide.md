# GovKloud Production Deployment Guide

Step-by-step guide for going from a verified staging deployment to a hardened production environment.

---

## Prerequisites

- ✅ Staging is fully working at `https://govkloud-app-staging.azurewebsites.net`
- ✅ All migrations run on staging
- ✅ Azure resources provisioned (App Service, MySQL, Resource Group)
- ✅ GitHub Actions deploying to staging slot

---

## Step 1: Mark Critical Settings as Slot-Specific

Slot settings **stay with the slot** during a swap (they don't move with the code). This prevents production values from swapping into staging and vice versa.

**In Azure Portal** → `govkloud-app` → **Configuration** → **Application settings**

Check the **"Deployment slot setting"** checkbox for these on the **production** slot:

| Setting | Production Value | Why Slot-Specific |
|---|---|---|
| `APP_ENV` | `production` | Must never be `staging` in prod |
| `APP_DEBUG` | `false` | Never expose errors in prod |
| `APP_URL` | `https://govkloud.com` | Different domain per slot |
| `APP_KEY` | *(generate unique)* | Security: separate keys per slot |
| `DB_DATABASE` | `govkloud_production` | Separate database per slot |
| `DB_PASSWORD` | *(prod password)* | Different credentials per slot |

**Also mark these as slot-specific on the staging slot:**

| Setting | Staging Value |
|---|---|
| `APP_ENV` | `staging` |
| `APP_DEBUG` | `true` |
| `APP_URL` | `https://govkloud-app-staging.azurewebsites.net` |
| `DB_DATABASE` | `govkloud_staging` |

---

## Step 2: Configure Production Environment Variables

**In Azure Portal** → `govkloud-app` (production, not staging) → **Configuration** → **Application settings**

```json
[
  { "name": "APP_NAME", "value": "GovKloud", "slotSetting": false },
  { "name": "APP_ENV", "value": "production", "slotSetting": true },
  { "name": "APP_KEY", "value": "base64:GENERATE_A_NEW_KEY", "slotSetting": true },
  { "name": "APP_DEBUG", "value": "false", "slotSetting": true },
  { "name": "APP_URL", "value": "https://govkloud.com", "slotSetting": true },
  { "name": "DB_CONNECTION", "value": "mysql", "slotSetting": false },
  { "name": "DB_HOST", "value": "govkloud-db.mysql.database.azure.com", "slotSetting": false },
  { "name": "DB_PORT", "value": "3306", "slotSetting": false },
  { "name": "DB_DATABASE", "value": "govkloud_production", "slotSetting": true },
  { "name": "DB_USERNAME", "value": "dbadmin", "slotSetting": false },
  { "name": "DB_PASSWORD", "value": "YOUR_PROD_DB_PASSWORD", "slotSetting": true },
  { "name": "MYSQL_ATTR_SSL_CA", "value": "/etc/ssl/certs/ca-certificates.crt", "slotSetting": false },
  { "name": "CACHE_DRIVER", "value": "database", "slotSetting": false },
  { "name": "SESSION_DRIVER", "value": "database", "slotSetting": false },
  { "name": "QUEUE_CONNECTION", "value": "database", "slotSetting": false },
  { "name": "LOG_CHANNEL", "value": "stack", "slotSetting": false },
  { "name": "LOG_LEVEL", "value": "error", "slotSetting": false },
  { "name": "STRIPE_KEY", "value": "pk_live_...", "slotSetting": true },
  { "name": "STRIPE_SECRET", "value": "sk_live_...", "slotSetting": true },
  { "name": "WEBSITE_DOCUMENT_ROOT", "value": "/public", "slotSetting": false }
]
```

> [!CAUTION]
> Use **live** Stripe keys for production, **test** keys for staging. Mark them as slot-specific.

### Generate a New APP_KEY

Run locally:
```bash
php artisan key:generate --show
```
Copy the output (e.g., `base64:abc123...`) into the `APP_KEY` setting for production.

---

## Step 3: Create the Production Database

Connect to Azure MySQL and create the production database:

```bash
az mysql flexible-server connect \
  --name govkloud-db \
  --admin-user dbadmin \
  --admin-password "YOUR_PASSWORD"
```

Or via any MySQL client:
```sql
CREATE DATABASE govkloud_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## Step 4: Set Up the Production Startup Script

SSH into the **production** slot:

```bash
az webapp ssh --resource-group govkloud-rg --name govkloud-app
```

Create the same startup script:

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

# Disable error interception
sed -i 's/fastcgi_intercept_errors on/fastcgi_intercept_errors off/g' /etc/nginx/sites-enabled/default

# Apply changes
nginx -s reload
EOF

chmod +x /home/startup.sh
exit
```

Set it as startup command:
```bash
az webapp config set \
  --resource-group govkloud-rg \
  --name govkloud-app \
  --startup-file "/home/startup.sh"
```

> [!NOTE]
> The startup script must exist on both slots because `/home` is separate per slot.

---

## Step 5: Update GitHub Actions for Automated Migrations

Update `.github/workflows/azure-deploy.yml` to run migrations automatically after deploy:

```yaml
name: Deploy to Azure App Service

on:
  push:
    branches:
      - main
      - master

jobs:
  build-and-deploy:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout code
        uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: mbstring, xml, ctype, json, bcmath, pdo, mysql

      - name: Install Composer dependencies
        run: composer install --no-dev --optimize-autoloader --no-interaction

      - name: Create deployment package
        run: |
          zip -r deploy.zip . \
            -x "*.git*" \
            -x "node_modules/*" \
            -x "tests/*" \
            -x ".env*" \
            -x "storage/logs/*" \
            -x "storage/framework/cache/*" \
            -x "storage/framework/sessions/*" \
            -x "storage/framework/views/*"

      - name: Deploy to Staging Slot
        uses: azure/webapps-deploy@v2
        with:
          app-name: 'govkloud-app'
          slot-name: 'staging'
          publish-profile: ${{ secrets.AZURE_PUBLISH_PROFILE_STAGING }}
          package: deploy.zip

      - name: Run Migrations on Staging
        uses: azure/CLI@v1
        with:
          inlineScript: |
            az webapp ssh --resource-group govkloud-rg \
              --name govkloud-app --slot staging \
              --command "cd /home/site/wwwroot && php artisan migrate --force"

      - name: Verify Staging
        run: |
          sleep 30
          STATUS=$(curl -o /dev/null -s -w "%{http_code}" https://govkloud-app-staging.azurewebsites.net)
          if [ "$STATUS" -ne 200 ]; then
            echo "❌ Staging returned HTTP $STATUS"
            exit 1
          fi
          echo "✅ Staging is healthy (HTTP 200)"
```

> [!IMPORTANT]
> Add `AZURE_CREDENTIALS` secret to GitHub for the `azure/CLI` action. Generate with:
> ```bash
> az ad sp create-for-rbac --name "github-deploy" \
>   --role contributor \
>   --scopes /subscriptions/YOUR_SUB_ID/resourceGroups/govkloud-rg \
>   --sdk-auth
> ```

---

## Step 6: Run Migrations on Production

Before the first swap, run migrations on the production database:

```bash
az webapp ssh --resource-group govkloud-rg --name govkloud-app
```

```bash
cd /home/site/wwwroot
php artisan migrate --force
```

> [!WARNING]
> After the first deployment, migrations should run via CI/CD (Step 5), not manually.

---

## Step 7: Swap Staging to Production

Once staging is verified:

```bash
az webapp deployment slot swap \
  --resource-group govkloud-rg \
  --name govkloud-app \
  --slot staging \
  --target-slot production
```

This performs a **zero-downtime swap** — staging code goes to production, production code goes to staging. Slot-specific settings stay where they are.

### Verify After Swap

```bash
# Check production
curl -I https://govkloud.com

# Check logs if issues
az webapp log tail --resource-group govkloud-rg --name govkloud-app
```

---

## Step 8: Set Up Custom Domain (Optional)

### Add Domain to Azure

```bash
az webapp config hostname add \
  --resource-group govkloud-rg \
  --webapp-name govkloud-app \
  --hostname govkloud.com
```

### Add SSL Certificate (Free Managed)

```bash
az webapp config ssl create \
  --resource-group govkloud-rg \
  --name govkloud-app \
  --hostname govkloud.com
```

### DNS Records

At your domain registrar, add:

| Type | Name | Value |
|---|---|---|
| A | `@` | Azure App Service IP |
| CNAME | `www` | `govkloud-app.azurewebsites.net` |
| TXT | `asuid` | Domain verification ID from Azure |

---

## Ongoing Deployment Workflow

After initial setup, the daily workflow is:

```
1. Push code to master branch
2. GitHub Actions builds & deploys to staging
3. Migrations run automatically on staging DB
4. Staging health check passes
5. Manually verify at staging URL
6. Swap staging → production (manual or automated)
```

### Quick Swap Command

```bash
az webapp deployment slot swap \
  --resource-group govkloud-rg \
  --name govkloud-app \
  --slot staging \
  --target-slot production
```

### Rollback (If Something Goes Wrong)

Swap again — the old production code is now in staging:

```bash
az webapp deployment slot swap \
  --resource-group govkloud-rg \
  --name govkloud-app \
  --slot staging \
  --target-slot production
```

---

## Production Checklist

Before going live, verify:

- [ ] `APP_DEBUG=false` on production
- [ ] `APP_ENV=production` on production
- [ ] `APP_KEY` is unique per slot
- [ ] `DB_DATABASE` points to `govkloud_production`
- [ ] Stripe uses **live** keys (not test)
- [ ] `MYSQL_ATTR_SSL_CA` is set
- [ ] Migrations run on production DB
- [ ] Startup script exists on production slot
- [ ] Custom domain and SSL configured
- [ ] `LOG_LEVEL=error` (not debug)

---

## Staging vs Production Comparison

| Setting | Staging | Production |
|---|---|---|
| `APP_ENV` | `staging` | `production` |
| `APP_DEBUG` | `true` | `false` |
| `APP_URL` | `https://govkloud-app-staging.azurewebsites.net` | `https://govkloud.com` |
| `DB_DATABASE` | `govkloud_staging` | `govkloud_production` |
| `LOG_LEVEL` | `debug` | `error` |
| `STRIPE_KEY` | `pk_test_...` | `pk_live_...` |
| `STRIPE_SECRET` | `sk_test_...` | `sk_live_...` |
