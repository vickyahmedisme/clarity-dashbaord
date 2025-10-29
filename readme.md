# Laravel 12 + Vue App

A Laravel 12 application with Vue on the frontend and a simple Metrics API.

## Quick start
- Requirements: PHP 8.4+, Composer, Node 22+, npm, SQLite (or another DB).
- Install dependencies:
  - `composer install`
  - `npm install`
- Environment:
  - Copy `.env.example` to `.env`
  - Set `APP_URL`, `APP_NAME`, and database settings (defaults work with `database/database.sqlite`)
  - `php artisan key:generate`
- Database:
  - Create `database/database.sqlite` if it does not exist
  - `php artisan migrate`
- Run:
  - `npm run dev` (Vite)
  - `php artisan serve`

## Metrics API
All endpoints return JSON. Base path: `/api/metrics`.

- GET `/api/metrics/monthly-sales`
  - Response: `{ "label": "Monthly Sales", "value": number }`

- GET `/api/metrics/open-deals`
  - Response: `{ "label": "Open Deals", "value": number }`

- GET `/api/metrics/active-accounts`
  - Response: `{ "label": "Active Accounts", "value": number }`

- GET `/api/metrics/daily-sales`
  - Response: `{ "label": "Daily Sales (last 30 days)", "values": [{date: number}] }`

## Development notes
- API routes: `routes/api.php` under the `metrics` prefix
- Controller: `App/Http/Controllers/MetricsController.php`
- Frontend: Vite + Vue in `resources/js`

## Testing
- `php artisan test` or `./vendor/bin/pest`

## License
MIT
