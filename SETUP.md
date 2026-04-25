# JobRango Setup Guide

JobRango is a Laravel 12.43.1 application using Botble CMS JobBox. The active job system lives in `platform/plugins/job-board`, the active theme is `platform/themes/jobbox`, and the public entry point is `public/index.php`.

Use this guide for local setup, staging setup, and the first usable test run.

## Requirements

- PHP: `^8.2|^8.3` as declared in `composer.json`.
- Composer: Composer 2 is recommended.
- Database: MySQL or MariaDB.
- Web server: Apache or Nginx pointing to the `public` directory, or Laravel's local development server.
- Node/npm: required only when rebuilding frontend assets.
- Composer-declared PHP extensions: `curl`, `gd`, `json`, `pdo`, and `zip`.
- Common Laravel/Botble hosting extensions to confirm: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, and `bcmath` if payment or numeric integrations are added later.

## Key Dependencies

- Laravel framework: `12.43.1`
- Botble platform packages:
  - `botble/platform`
  - `botble/theme`
  - `botble/menu`
  - `botble/page`
  - `botble/seo-helper`
  - `botble/plugin-management`
  - `botble/installer`
  - `botble/widget`
- Job system plugin: `platform/plugins/job-board`
- Active theme: `platform/themes/jobbox`
- Other important packages include `laravel/sanctum`, `guzzlehttp/guzzle`, `predis/predis`, `doctrine/dbal`, `barryvdh/laravel-dompdf`, `maatwebsite/excel`, and `league/flysystem-aws-s3-v3`.

## Local Setup

1. Install PHP dependencies:

```bash
composer install
```

2. Create the local environment file:

```bash
cp .env.example .env
```

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

3. Configure the required `.env` values:

```env
APP_NAME="JobRango"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
ADMIN_DIR=admin

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jobrango
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@jobrango.example"
MAIL_FROM_NAME="${APP_NAME}"
```

4. Generate the Laravel application key:

```bash
php artisan key:generate
```

5. Create a local database named `jobrango`.

6. Import the bundled database dump for the first usable run:

```bash
mysql -u root -p jobrango < database.sql
```

You can also import `database.sql` through phpMyAdmin or another database tool.

7. Create the public storage link:

```bash
php artisan storage:link
```

8. Publish Botble assets:

```bash
php artisan cms:publish:assets
```

9. Clear cached framework state:

```bash
php artisan optimize:clear
```

10. Start the local development server:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Frontend URL:

```text
http://127.0.0.1:8000
```

Admin URL, using the default `ADMIN_DIR=admin`:

```text
http://127.0.0.1:8000/admin/login
```

## Frontend Asset Setup

Assets are managed with Laravel Mix. Node/npm is required when assets need to be rebuilt.

Install npm dependencies:

```bash
npm install
```

Build production assets:

```bash
npm run prod
```

Build development assets:

```bash
npm run dev
```

The root `webpack.mix.js` can build platform core assets, packages, plugins, and themes. The JobRango active theme also has its own asset build file at `platform/themes/jobbox/webpack.mix.js`.

The generated `public/vendor` and `public/themes` files are runtime/build outputs and are ignored by git. Recreate them with `php artisan cms:publish:assets` and, when needed, `npm run prod`.

## Database Setup Notes

For the first usable local or staging setup, import `database.sql`. The dump contains the installed Botble CMS state, settings, pages, theme options, media records, job-board demo data, and migration history.

The project also contains migrations and seeders. They are useful for development reference, but a migration-only install may not reproduce the same usable CMS/theme state as `database.sql`. Use migration-only setup only for a deliberate clean rebuild.

Seeders include demo accounts, companies, jobs, categories, locations, pages, blog posts, widgets, and theme options. Do not run seeders on a production database unless you intend to create demo content.

## Demo Credentials

The database dump includes demo credentials for testing only.

Admin login:

```text
URL: /admin/login
Username: admin
Password: 12345678
```

The demo admin user should be replaced or updated immediately after setup.

The job-board account seed data also uses the demo password `12345678` for front-office sample accounts. Example accounts include:

```text
employer@archielite.com
job_seeker@archielite.com
```

Delete or replace demo accounts before launch.

## Admin Path

The admin path is configured by:

```env
ADMIN_DIR=admin
```

The default local admin URL is:

```text
http://127.0.0.1:8000/admin/login
```

If `ADMIN_DIR` changes, the admin URL changes with it.

Important admin settings pages:

- General settings: `/admin/settings/general`
- Email settings: `/admin/settings/email`
- Email templates: `/admin/settings/email/templates`
- Theme options: `/admin/theme/options`
- Pages: `/admin/pages`
- Media manager: `/admin/media`
- Job board settings: `/admin/job-board/settings/general`
- Admin users: `/admin/system/users`

## Storage and Media Setup

Run this command after configuring `.env`:

```bash
php artisan storage:link
```

Laravel's public disk uses:

```text
storage/app/public
public/storage
```

Botble media is managed through the admin media manager and the `media_files` table. The imported demo media references paths such as:

- `general/logo.png`
- `general/logo-light.png`
- `general/favicon.png`
- `general/logo-company.png`
- `resume/01.pdf`
- `avatars/*.png`
- `companies/*.png`
- `jobs/*.png`
- `pages/*`
- `news/*`
- `galleries/*`

Logo and favicon locations are configurable from Botble admin:

- Frontend logo and favicon: `/admin/theme/options`
- Admin logo and favicon: `/admin/settings/general`
- Email logo: `/admin/settings/email/templates`

## Mail Setup

Local development can use log mail:

```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@jobrango.example"
MAIL_FROM_NAME="${APP_NAME}"
```

SMTP example:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Botble can also configure mail from the admin dashboard. The project enables email configuration from the admin panel by default, so admin settings may override Laravel mail config at runtime.

Admin paths:

- Mail settings: `/admin/settings/email`
- Email templates: `/admin/settings/email/templates`

Use the send test email action on the email settings page after SMTP values are configured.

## Queue, Cache, and Session Notes

For local testing, the default file/session/cache and synchronous queue settings are acceptable.

For staging or production, review these values:

```env
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

If you switch to `database` or `redis` queues, configure the backing service and run a queue worker:

```bash
php artisan queue:work
```

Clear caches after changing `.env` or admin-driven config:

```bash
php artisan optimize:clear
```

For optimized staging or production deployments, after the environment is correct:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Staging Setup

1. Point the web server document root to `public`.
2. Install dependencies without development packages:

```bash
composer install --no-dev --optimize-autoloader
```

3. Copy and configure `.env`:

```env
APP_NAME="JobRango"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://staging.example.com
ADMIN_DIR=admin
```

4. Configure database, mail, cache, session, and queue values.
5. Generate `APP_KEY` only if this is a fresh environment:

```bash
php artisan key:generate
```

6. Import `database.sql` for the first staging test database.
7. Run:

```bash
php artisan storage:link
php artisan cms:publish:assets
php artisan optimize:clear
```

8. Build assets only if the staging server is responsible for compiling assets:

```bash
npm install
npm run prod
```

9. Confirm write permissions for:

```text
storage
bootstrap/cache
public/storage
```

10. Log in to admin, change demo credentials, configure mail, replace media, and remove demo content before any public launch.

