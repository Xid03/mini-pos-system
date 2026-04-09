# Render + Supabase Deployment

This branch is prepared specifically for a Render deployment that uses Supabase Postgres instead of the default MySQL setup from `main`.

## What is different on this branch

- environment-variable database configuration is enabled
- Postgres-compatible SQL files are included for Supabase
- a Dockerfile and Render blueprint are included for deployment
- a lightweight database heartbeat is included through GitHub Actions

Keep your portfolio-friendly `main` branch unchanged, and deploy Render from this branch only.

## 1. Create a Supabase project

Create a new Supabase project, then open:

- `Project Settings` -> `Database`

Copy the direct or session-style Postgres connection string.

Use the direct/session connection mode so prepared statements continue to work reliably. Avoid the transaction pooler string for this app.

## 2. Import the Postgres schema and seeds

Run these files in the Supabase SQL editor in this order:

1. `database/schema/supabase_postgres.sql`
2. `database/seeds/supabase_step2_sample_users.sql`
3. optionally `database/seeds/supabase_step9_sample_data.sql`

The optional Step 9 seed adds products, inventory movements, and sample sales so the dashboard, POS history, and reports look populated after deployment.

## 3. Push this deployment branch

Push this branch to GitHub, for example:

```powershell
git push -u origin codex-render-deploy
```

Render should be connected to this deployment branch, not to `main`.

## 4. Create the Render web service

In Render:

1. click `New +`
2. choose `Blueprint` or `Web Service`
3. connect your GitHub repository
4. pick the `codex-render-deploy` branch

If you use Blueprint, Render can read the included `render.yaml`.

If you use manual setup:

- Environment: `Docker`
- Dockerfile Path: `./Dockerfile`
- Health Check Path: `/login.php`

## 5. Set Render environment variables

Set these values in Render:

- `APP_ENV=production`
- `DB_DRIVER=pgsql`
- `DATABASE_URL=<your direct Supabase Postgres connection string>`

`DATABASE_URL` should be the direct/session Supabase Postgres connection string.

## 6. Deploy

Render will build from the included `Dockerfile`.

The startup script automatically binds Apache to Render's assigned `PORT`.

After the deploy finishes, open:

- `/login.php`

Use the seeded accounts:

- `admin@minipos.local` / `Admin@123`
- `cashier@minipos.local` / `Cashier@123`

## 7. Heartbeat

The included GitHub Actions workflow runs `scripts/heartbeat.php` twice each week to gently touch the database.

Current schedule:

- every Monday
- every Thursday

To enable it:

1. push this branch to GitHub
2. add the repository secret `DATABASE_URL`
3. keep GitHub Actions enabled for the repository

The heartbeat script performs a tiny read against the Supabase database so you have a simple recurring activity check without changing application data.

## 8. Optional custom domain

After the Render deploy succeeds, add your custom domain in Render and point the matching DNS record from your domain provider to the Render service.

## Files used for this deployment branch

- `Dockerfile`
- `render.yaml`
- `docker/start-apache.sh`
- `database/schema/supabase_postgres.sql`
- `database/seeds/supabase_step2_sample_users.sql`
- `database/seeds/supabase_step9_sample_data.sql`
- `scripts/heartbeat.php`
- `.github/workflows/database-heartbeat.yml`
