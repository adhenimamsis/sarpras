# Deploy Run Log (2026-03-11)

## Executed
- Updated `.env` to production mode (`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://pkm-bendan.pekalongankota.go.id`, `MAIL_MAILER=smtp`).
- `composer install --no-dev --optimize-autoloader`
- `npm ci`
- `npm run build`
- `php artisan migrate --force`
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`
- `php artisan filament:cache-components`
- `php artisan queue:restart`

## Local Smoke (post-deploy style)
- `GET /` -> `200`
- `GET /login` -> `200`
- `GET /admin/login` -> `200`
- `GET /admin` (guest) -> `302` to `/admin/login`
- `GET /laporan-bulanan-pdf` (guest) -> `302` to `/login`

## Production Domain Smoke Attempt
- Target: `https://pkm-bendan.pekalongankota.go.id`
- Result: all endpoints returned HTTP code `000` from this environment (network/DNS access unavailable).
- Note: escalated outbound network check was attempted but not approved in this session.
