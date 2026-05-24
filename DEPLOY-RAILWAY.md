# Deploying RentWise to Railway (demo)

This app is Laravel 12 (PHP 8.2) + Vite/Tailwind, served by **Laravel Octane on
FrankenPHP** (the app stays booted in memory between requests). It deploys to
Railway via the `Dockerfile` in the project root — Railway auto-detects it and
builds the image.

> **Note:** This is a single-service demo setup. Reverb websockets and queue
> workers are **not** run here (`BROADCAST_CONNECTION=log`, `QUEUE_CONNECTION=database`
> jobs only run when something processes them). Everything else works.

## 1. Push the repo to GitHub

```bash
git add Dockerfile .dockerignore docker/entrypoint.sh DEPLOY-RAILWAY.md
git commit -m "Add Railway Docker deploy config"
git push
```

## 2. Create the Railway service

1. railway.app → **New Project** → **Deploy from GitHub repo** → pick this repo.
2. Railway detects the `Dockerfile` and builds it automatically.
3. Your MySQL database already lives on Railway — you can add it to this same
   project (**+ New → Database → MySQL**, or reference the existing one).

## 3. Set environment variables

In the service's **Variables** tab, add the following. Copy `APP_KEY` and the
`DB_*` values from your local `.env` (do **not** upload the `.env` file).

| Variable | Value |
|---|---|
| `APP_NAME` | `RentWise` |
| `APP_ENV` | `production` |
| `APP_KEY` | *(copy from local `.env`)* |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://<your-railway-domain>` |
| `LOG_CHANNEL` | `stack` |
| `LOG_LEVEL` | `error` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | *(your Railway MySQL host)* |
| `DB_PORT` | *(your Railway MySQL port)* |
| `DB_DATABASE` | `railway` |
| `DB_USERNAME` | `root` |
| `DB_PASSWORD` | *(copy from local `.env`)* |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `BROADCAST_DRIVER` | `log` |
| `BROADCAST_CONNECTION` | `log` |
| `FILESYSTEM_DISK` | `local` |
| `MAIL_MAILER` | `log` |

> If you add the MySQL DB inside the same Railway project, you can reference its
> private variables instead, e.g. `DB_HOST=${{MySQL.MYSQLHOST}}`,
> `DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}`, etc. — faster and not exposed publicly.

## 4. Generate a domain

Service → **Settings → Networking → Generate Domain**. Railway sets `PORT`
automatically; the container already binds to it. Put that URL in `APP_URL`.

## 5. Deploy

Railway builds on every push to the connected branch. On boot the container:

1. caches config,
2. runs `php artisan migrate --force` (creates the sessions/cache/queue tables too),
3. links storage,
4. serves the app on `$PORT` via `php artisan octane:start --server=frankenphp`.

After a deploy, workers start fresh, so no `octane:reload` is needed. To tune
throughput vs. memory, set `OCTANE_WORKERS` (default: one per CPU) and
`OCTANE_MAX_REQUESTS` (default: 500) in the Railway Variables tab.

## Test the image locally (optional)

```bash
docker build -t rentwise .
docker run --rm -p 8080:8080 --env-file .env -e PORT=8080 rentwise
# open http://localhost:8080
```

## Security reminder

Your live DB password currently sits in local `.env` (correctly gitignored).
Set it through Railway's Variables UI only, and rotate it if it's ever been shared.
