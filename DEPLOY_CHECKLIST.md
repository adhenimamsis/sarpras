# Deploy Checklist - SIM Sarpras Puskesmas

## Status Snapshot (2026-03-13)
Status ini berdasarkan verifikasi terbaru di `DEPLOY_RUN_LOG_2026-03-13.md`.

### Re-Verification (Sesi 2026-03-13)
- [x] `composer quality` -> lint pass, 35 test pass.
- [x] `composer doctor` -> status `ok` (app, database, cache, storage).
- [x] `npm run quality` -> build produksi frontend berhasil.
- [x] `php artisan migrate --force` -> tidak ada migrasi baru.
- [x] `php artisan config:cache`
- [x] `php artisan route:cache`
- [x] `php artisan view:cache`
- [x] `php artisan filament:cache-components`
- [x] `php artisan queue:restart`
- [ ] Smoke domain produksi eksternal dari sesi ini (akses jaringan eksternal tidak tersedia di environment ini).

### Catatan Environment Lokal Saat Verifikasi
- [x] Mode lokal aktif (`APP_ENV=local`, `APP_DEBUG=true`, `MAIL_MAILER=log`, `APP_URL=http://localhost`).
- [ ] Uji final dengan `.env` produksi perlu dijalankan langsung di server produksi.

---

## Status Snapshot (2026-03-11)
Status ini berdasarkan eksekusi lokal yang tercatat di `DEPLOY_RUN_LOG_2026-03-11.md`.

### 1) Pre-Deploy (Manual)
- [ ] Backup database produksi.
- [ ] Backup folder `storage/app` (dokumen, export, lampiran).
- [ ] Pastikan worker queue sudah disiapkan (Supervisor / service manager).
- [ ] Pastikan cron scheduler aktif: `* * * * * php /path/to/artisan schedule:run`.

### 2) Build & Verify (Lokal/CI)
- [x] Jalankan test: `php artisan test` (31 test passed pada 2026-03-11).
- [x] Build frontend: `npm run build`.
- [x] Validasi `public/build/manifest.json` ter-generate.
- [x] Validasi route utama lokal: `/`, `/login`, `/admin`, `/laporan-bulanan-pdf`.

### 3) Deploy Commands (Sudah Dijalankan di Sesi Ini)
- [x] `composer install --no-dev --optimize-autoloader`
- [x] `npm ci`
- [x] `npm run build`
- [x] `php artisan migrate --force`
- [x] `php artisan config:cache`
- [x] `php artisan route:cache`
- [x] `php artisan view:cache`
- [x] `php artisan filament:cache-components`
- [x] `php artisan queue:restart`

### 4) Post-Deploy Smoke Test
- [x] `GET /` -> `200` (lokal).
- [x] `GET /login` -> `200` (lokal).
- [x] `GET /admin/login` -> `200` (lokal).
- [x] `GET /admin` (guest) -> redirect ke `/admin/login` (lokal).
- [x] `GET /laporan-bulanan-pdf` (guest) -> redirect ke `/login` (lokal).
- [ ] Smoke domain produksi eksternal dari sesi ini (terblokir jaringan, HTTP `000`).

### 5) Security & Hardening
- [x] `APP_ENV=production`.
- [x] `APP_DEBUG=false`.
- [x] `MAIL_MAILER=smtp`.
- [x] `APP_URL=https://pkm-bendan.pekalongankota.go.id`.
- [ ] Logging produksi diverifikasi (channel + rotasi).
- [ ] Izin folder `storage` dan `bootstrap/cache` diverifikasi di server.

---

## Reusable Checklist (Untuk Deploy Berikutnya)
Gunakan list ini sebagai template kosong untuk rilis selanjutnya.

### Pre-Deploy
- [ ] Backup database produksi.
- [ ] Backup folder `storage/app`.
- [ ] Verifikasi `.env` produksi.
- [ ] Verifikasi worker queue dan cron scheduler.

### Build & Verify
- [ ] `composer quality`
- [ ] `npm run quality`
- [ ] Validasi endpoint kritikal.

### Deploy
- [ ] Pull release terbaru.
- [ ] Install dependency backend/frontend.
- [ ] Build asset.
- [ ] Migrate + cache optimize.
- [ ] Restart queue worker.

### Post-Deploy
- [ ] Smoke test endpoint utama.
- [ ] Cek login role utama (admin/staff/teknisi/kapus).
- [ ] Cek fitur laporan PDF.
