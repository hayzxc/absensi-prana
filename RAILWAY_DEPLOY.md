# Hosting Guide for Railway

This guide outlines the steps to deploy the `absensi-karyawan-gps-barcode` application to [Railway.app](https://railway.app/).

## Prerequisites

1.  **GitHub Repository**: Ensure your code is pushed to a GitHub repository.
2.  **Railway Account**: Sign up at [Railway.app](https://railway.app/).

## Step 1: Create Project & Database

1.  Go to your Railway Dashboard and click **"New Project"**.
2.  Select **"Deploy from GitHub repo"** and choose your repository.
3.  Click **"Add a Service"** (or right-click the canvas) and select **"Database"** -> **"MySQL"**.
    - This will create a MySQL service. Railway will automatically provide connection variables.

## Step 2: Configure Laravel Service

Click on your Laravel application service (the one connected to GitHub) to open its settings.

### 1. Environment Variables
Go to the **"Variables"** tab. You need to add the following variables.

**System Variables:**
- `APP_NAME`: `Absensi Karyawan`
- `APP_ENV`: `production`
- `APP_DEBUG`: `false`
- `APP_URL`: `https://${{RAILWAY_PUBLIC_DOMAIN}}` (This will auto-fill with your provided domain)
- `APP_KEY`: Generate a new key locally using `php artisan key:generate --show` and paste it here.
- `LOG_CHANNEL`: `stderr` (Recommended for Railway logs)

**Database Variables:**
*Choose ONE of the following options:*

**Option A: MySQL (Recommended for High Concurrency)**
- `DB_CONNECTION`: `mysql`
- `DB_HOST`: `${{MySQL.HOST}}`
- `DB_PORT`: `${{MySQL.PORT}}`
- `DB_DATABASE`: `${{MySQL.DATABASE}}`
- `DB_USERNAME`: `${{MySQL.USER}}`
- `DB_PASSWORD`: `${{MySQL.PASSWORD}}`

**Option B: SQLite (Simpler, requires Volume)**
- `DB_CONNECTION`: `sqlite`
- `DB_DATABASE`: `/app/storage/database.sqlite`
- **Crucial**: You MUST set up a Volume mounted at `/app/storage` (see Step 3) or your database will be wiped on every deploy.
- **Why not `database/database.sqlite`?** Locally, your database is in the `database/` folder. However, on Railway, that folder is read-only or reset on deploy. We use `/app/storage` because we mount a Volume there to keep your data safe.

**Storage (Important):**
- `FILESYSTEM_DISK`: `public` (or `local`)

### 2. Build & Start Commands
Go to the **"Settings"** tab -> **"Deploy"** section.

- **Build Command**: 
  ```bash
  npm run build && php artisan optimize
  ```
- **Start Command (MySQL)**: 
  ```bash
  php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT
  ```
- **Start Command (SQLite)**: 
  ```bash
  touch /app/storage/database.sqlite && php artisan migrate --force && php artisan db:seed --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT
  ```
  *(Note: This command runs migrations AND seeds the database automatically on every deploy. We ensure it's safe by checking for duplicate data in the seeders).*

## Step 3: Persistence (File Uploads & SQLite)

**Warning**: Railway's filesystem is ephemeral. This means if you restart or redeploy, **all uploaded files (and SQLite DB)** will be deleted unless you attach a Volume.

**Option A: Railway Volume (Required for SQLite)**
1.  In your Service Settings, go to **"Volumes"**.
2.  Click **"Add Volume"**.
3.  Mount Path: `/app/storage`
    - This ensures `database.sqlite` (if using Option B) and uploaded files in `storage/app` are saved.

**Option B: AWS S3 / R2 (For file uploads only)**
1.  Set `FILESYSTEM_DISK=s3` in Variables.
2.  Add AWS variables: `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`.

## Step 4: Generage Public Domain

1.  In Service Settings, go to the **"Networking"** tab.
2.  Click **"Generate Domain"** (or add your own Custom Domain).
3.  This domain will be linked to your `APP_URL`.

## Step 5: Depoyment

1.  Railway usually deploys automatically on push.
2.  Check the "Deployments" tab to see the build logs.
3.  Once the build is green, open your generated domain.

## Common Issues

- **404 on Assets**: Ensure `npm run build` runs in the Build Command.
- **500 Error**: Check the "Logs" tab. It's usually a missing Environment Variable (like `APP_KEY`) or DB connection issue.
- **Mixed Content (HTTP/HTTPS)**: Ensure `APP_URL` uses `https://` and if using a proxy, you might need to enforce HTTPS in `AppServiceProvider`.
