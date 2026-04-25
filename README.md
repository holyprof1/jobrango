# JobRango

JobRango is a Laravel/Botble job opportunity platform where companies post work opportunities and applicants apply through a simple user portal.

Tagline: Find your range of work.

## Current Stack

- Laravel 12.43.1
- Botble CMS
- Job board plugin: `platform/plugins/job-board`
- Active frontend theme: `platform/themes/jobbox`
- Database dump: `database.sql`
- Public entry point: `public/index.php`

The project is based on the upstream JobBox/Botble script. Visible defaults are being rebranded to JobRango, while internal Botble identifiers such as the `jobbox` theme folder and `Theme\Jobbox` namespace remain unchanged for compatibility.

## Local Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan cms:publish:assets
php artisan serve
```

Create a MySQL/MariaDB database, update `.env`, and import `database.sql` if you want the current seeded/demo content.

## Important Admin Paths

- `/admin/settings/general`: general site settings
- `/admin/theme/options`: theme options, SEO, logo, favicon, footer text
- `/admin/widgets`: footer and newsletter widgets
- `/admin/pages`: homepage and page content
- `/admin/settings/email`: SMTP/mail sender settings
- `/admin/settings/email/templates`: email template logo, footer, and job-board emails
- `/admin/job-board/settings/general`: job-board behavior settings

## Documentation

- `PROJECT_OVERVIEW.md`
- `CLEANUP_REPORT.md`
- `REBRANDING_REPORT.md`
