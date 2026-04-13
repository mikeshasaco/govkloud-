# GovKloud Deployment Commands Cheat Sheet

## Architecture Overview

```
git push origin master  →  GitHub Actions  →  Docker Build  →  ACR (staging tag)  →  Staging Slot
                                                                                         ↓
                                                                            az slot swap → Production
```

- **Staging**: `https://govkloud-app-staging.azurewebsites.net`
- **Production**: `https://govkloud.com` / `https://govkloud-app.azurewebsites.net`

---

## 1. Deploy Code to Staging (Automatic)

Just push to master — GitHub Actions handles the rest:

```bash
git add -A
git commit -m "your message"
git push origin master
```

This automatically:
- Builds a new Docker image
- Pushes it to `govkloudacr.azurecr.io/govkloud-app:staging`
- Restarts the staging slot

---

## 2. Promote Staging → Production (Manual)

After verifying staging looks good:

```bash
az webapp deployment slot swap \
  --name govkloud-app \
  --resource-group govkloud-rg \
  --slot staging \
  --target-slot production
```

---

## 3. Restart Slots

### Restart Staging
```bash
az webapp restart --name govkloud-app --resource-group govkloud-rg --slot staging
```

### Restart Production
```bash
az webapp restart --name govkloud-app --resource-group govkloud-rg
```

---

## 4. Check Slot Status

### View both slots
```bash
az webapp show --name govkloud-app --resource-group govkloud-rg --query "{hostname:defaultHostName, state:state}" -o table
az webapp deployment slot list --name govkloud-app --resource-group govkloud-rg -o table
```

### View container logs (staging)
```bash
az webapp log tail --name govkloud-app --resource-group govkloud-rg --slot staging
```

### View container logs (production)
```bash
az webapp log tail --name govkloud-app --resource-group govkloud-rg
```

---

## 5. Azure Blob Storage (No Rebuild Needed)

### Upload/replace a file (e.g. logo)
```bash
az storage blob upload \
  --account-name govkloudstorage \
  --container-name assets \
  --name govkloud-logo.png \
  --file ./path/to/local/file.png \
  --overwrite \
  --content-type image/png
```

### List blobs in a container
```bash
az storage blob list --account-name govkloudstorage --container-name assets -o table
```

### URL pattern
```
https://govkloudstorage.blob.core.windows.net/{container}/{filename}
```

---

## 6. Docker Image Management

### List images in ACR
```bash
az acr repository show-tags --name govkloudacr --repository govkloud-app -o table
```

### Manually build & push (if Actions is down)
```bash
docker build -t govkloudacr.azurecr.io/govkloud-app:staging .
az acr login --name govkloudacr
docker push govkloudacr.azurecr.io/govkloud-app:staging
az webapp restart --name govkloud-app --resource-group govkloud-rg --slot staging
```

---

## 7. Start/Stop Resources (Cost Control)

### Stop everything (saves money when not in use)
```bash
az webapp stop --name govkloud-app --resource-group govkloud-rg
az webapp stop --name govkloud-app --resource-group govkloud-rg --slot staging
```

### Start everything
```bash
az webapp start --name govkloud-app --resource-group govkloud-rg
az webapp start --name govkloud-app --resource-group govkloud-rg --slot staging
```

---

## Quick Reference

| What you want to do | Command |
|---|---|
| Deploy to staging | `git push origin master` (automatic) |
| Promote to production | `az webapp deployment slot swap ...` |
| Restart production | `az webapp restart --name govkloud-app -g govkloud-rg` |
| Restart staging | `az webapp restart --name govkloud-app -g govkloud-rg --slot staging` |
| Update logo (no rebuild) | `az storage blob upload ...` |
| View prod logs | `az webapp log tail --name govkloud-app -g govkloud-rg` |
| View staging logs | `az webapp log tail --name govkloud-app -g govkloud-rg --slot staging` |
