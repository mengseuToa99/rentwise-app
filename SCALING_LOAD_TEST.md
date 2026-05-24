# Scaling + Load-Test Quick Start

## 1) Switch runtime stores to Redis

Set these in production:

```env
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
BROADCAST_CONNECTION=redis
```

Keep `DB_CONNECTION=mysql`.

## 2) Apply index pack

```bash
php artisan migrate
```

This repo now includes a migration that adds indexes for common dashboard/list queries and DB queue polling.

## 3) Seed a 10k-user dataset

```bash
php artisan db:seed --class=Database\\Seeders\\LoadTestSeeder
```

Optional tuning env vars:

- `LOAD_TEST_TOTAL_USERS` (default `10000`)
- `LOAD_TEST_LANDLORDS` (default `200`)
- `LOAD_TEST_PROPERTIES_PER_LANDLORD` (default `4`)
- `LOAD_TEST_UNITS_PER_PROPERTY` (default `8`)
- `LOAD_TEST_INVOICE_MONTHS` (default `2`)
- `LOAD_TEST_MAINTENANCE_RATE` (default `0.12`)

Generated users use password: `password`.

## 4) Run load test

```bash
./scripts/run-load-test.sh
```

Defaults:

- `BASE_URL=http://127.0.0.1:8000`
- `VUS=200`
- `DURATION=2m`

Override example:

```bash
BASE_URL=http://127.0.0.1:8000 VUS=500 DURATION=5m ./scripts/run-load-test.sh
```

If `k6` exists, the script uses `scripts/k6-load-test.js`. Otherwise it falls back to `hey`, `ab`, or `wrk`.
