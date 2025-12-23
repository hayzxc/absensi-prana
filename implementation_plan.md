# Railway SQLite Configuration Plan

The goal is to configure the Railway deployment to use SQLite via CLI/Files, ensuring the database file persists and is created if missing.

## User Review Required
- **Volume Creation**: The user MUST manually create a Volume in Railway web interface for `/app/storage`. I cannot do this via CLI reliably without Service IDs.
- **Environment Variables**: I will set `DB_CONNECTION=sqlite` and `DB_DATABASE=/app/storage/database.sqlite`.

## Proposed Changes

### Configuration
#### [NEW] [railway.toml](file:///d:/File%20Kerja/Apps/absen/absensi-karyawan-gps-barcode/railway.toml)
- Create a `railway.toml` file to define the `build` and `deploy` commands.
- **Build**: `npm run build && php artisan optimize`
- **Deploy**: `touch /app/storage/database.sqlite && php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT`

## Verification Plan
### Automated Verification
- Run `railway run php artisan migrate:status` (once deployed) - *Wait, this relies on deployment.*
- I will verify the `railway.toml` content.

### Manual Verification
- User runs `railway up`.
- User checks Railway Dashboard to verify variables are set.
- User creates the Volume in Railway Dashboard.
