# SIM Sarpras Puskesmas

Aplikasi manajemen sarana dan prasarana Puskesmas berbasis Laravel + Filament dengan fokus ke operasional harian, pelaporan, dan monitoring.

## Stack
- PHP 8.3
- Laravel 11
- Filament 3
- SQLite/MySQL
- Vite + Tailwind CSS

## Prasyarat
- PHP 8.3+
- Composer 2
- Node.js 20+ dan npm

## Setup Lokal
```bash
cp .env.example .env
composer install
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --ansi
npm ci
```

Jalankan aplikasi:
```bash
php artisan serve
npm run dev
```

## Perintah Utama
- `composer format`: auto-format PHP code dengan Laravel Pint.
- `composer lint`: verifikasi style code tanpa mengubah file.
- `composer test`: jalankan seluruh test backend.
- `composer quality`: quality gate backend (`lint + test`).
- `composer doctor`: health diagnostic operasional via CLI.
- `npm run quality`: build produksi frontend (validasi aset Vite).

## Health Monitoring
- `GET /up`: liveness check bawaan Laravel.
- `GET /health`: health check aplikasi dengan status JSON untuk:
  - aplikasi
  - database
  - cache
  - storage permissions
- `php artisan ops:doctor`: health check yang sama via terminal.

Contoh:
```bash
php artisan ops:doctor --json
curl http://127.0.0.1:8000/health
```

## Testing
- Konfigurasi test memakai SQLite file `database/testing.sqlite` (lihat `phpunit.xml`).
- Isolasi ini mencegah test menyentuh database produksi.

## CI Quality Gate
Workflow: `.github/workflows/ci-quality.yml`

Pipeline menjalankan:
- validasi `composer.json`
- instalasi dependency backend dan frontend
- migrasi database CI
- `composer quality`
- `composer doctor`
- `npm run quality`

## Deploy
Gunakan dokumen:
- `DEPLOY_CHECKLIST.md`
- `DEPLOY_RUN_LOG_2026-03-11.md`
- `DEPLOY_RUN_LOG_2026-03-13.md`

## Deploy Render (Tanpa Domain Pribadi)
Project ini sudah disiapkan untuk deploy ke Render dengan subdomain bawaan `onrender.com`.

File terkait:
- `render.yaml`
- `scripts/render-build.sh`
- `scripts/render-start.sh`

Langkah cepat:
1. Push repo ini ke GitHub.
2. Di Render pilih `New +` -> `Blueprint` -> pilih repo.
3. Render akan membaca `render.yaml` dan membuat:
   - 1 web service `sarpras-puskesmas`
   - 1 PostgreSQL `sarpras-db`
4. Setelah service pertama kali live, update env `APP_URL` ke URL Render yang asli (misal `https://sarpras-puskesmas.onrender.com`), lalu redeploy.

Catatan:
- `LARAVEL_STORAGE_PATH` diarahkan ke disk persistent `/var/data/storage` agar upload file tidak hilang saat redeploy.
- Migrate + cache dijalankan otomatis saat start.
