# JobRango Project Overview

## Purpose

JobRango is a job opportunity platform for companies, employers, agencies, and outsourcing firms to publish work opportunities while applicants can register, apply for jobs, save jobs, and track applications through an account portal.

The current installed script is still the upstream JobBox/Botble implementation. No major JobRango-specific features have been added yet; this cleanup records the current architecture before custom development begins.

## Technology Stack

- PHP application using Laravel Framework 12.43.1.
- Botble CMS platform modules under `platform/core`, `platform/packages`, and `platform/plugins`.
- Job portal domain plugin under `platform/plugins/job-board`.
- Frontend theme under `platform/themes/jobbox`.
- MySQL/MariaDB database, with a bundled SQL dump in `database.sql`.
- Composer for PHP dependencies.
- npm and Laravel Mix/Webpack for frontend asset builds.
- Laravel Sanctum is available for protected API endpoints.

## Architecture

This is a Laravel/Botble CMS app rather than WordPress, CodeIgniter, or plain PHP.

The root Laravel bootstrap files are standard:

- `artisan` is the CLI entry point.
- `bootstrap/app.php` configures Laravel routing and bootstrapping.
- `public/index.php` is the HTTP front controller.
- root `.htaccess` rewrites web requests into `public/`.
- `public/.htaccess` sends non-file requests to `public/index.php`.

Most project behavior is loaded by Botble service providers from modules in `platform`.

## Main Folder Structure

- `app/`: Thin Laravel app layer with base providers, middleware, and `App\Models\User`.
- `bootstrap/`: Laravel bootstrap files and cache placeholder.
- `config/`: Laravel configuration for app, auth, cache, database, filesystems, mail, queues, sessions, and services.
- `database/`: Laravel migrations, seeders, factories, and app-level database metadata.
- `database.sql`: Bundled MySQL/MariaDB dump for the installed demo/current database.
- `lang/`: Base language files. Published vendor language output is ignored except `lang/en`.
- `platform/core/`: Botble CMS core, including ACL/admin auth, dashboard, media, settings, tables, and base services.
- `platform/packages/`: Botble packages such as menu, page, slug, theme, widget, sitemap, SEO helper, installer, and optimize.
- `platform/plugins/`: Feature plugins including job-board, blog, location, payment providers, social login, newsletter, analytics, contact, FAQ, gallery, and translation.
- `platform/plugins/job-board/`: Main job portal plugin with routes, controllers, models, forms, tables, notifications, listeners, migrations, views, and assets.
- `platform/themes/jobbox/`: Public theme, Blade templates/partials, theme functions, shortcode definitions, routes, widgets, and source public assets.
- `public/`: Web root. Only source/static entry files are committed; generated `public/vendor`, `public/themes`, and runtime `public/storage` are ignored.
- `resources/`: App-level frontend source files.
- `routes/`: Laravel app route files. Main business routes are provided by Botble modules and plugins.
- `storage/`: Laravel runtime storage placeholders. Logs, cache, sessions, compiled views, backups, and uploads are ignored.
- `tests/`: Test skeleton.

## Public Entry Point

The public entry point is `public/index.php`.

Deployment should point the web server document root to `public/`. If the hosting account points to the project root, the root `.htaccess` rewrites requests into `public/`.

## Routing

The top-level `routes/web.php` is intentionally minimal. Botble loads routes from platform modules.

Important route files:

- `platform/core/acl/routes/web.php`: Admin authentication and user/role flows.
- `platform/core/base/routes/web.php`: Botble base/admin routes.
- `platform/plugins/job-board/routes/web.php`: Admin job-board management routes.
- `platform/plugins/job-board/routes/account.php`: Applicant/employer account routes.
- `platform/plugins/job-board/routes/public.php`: Public job, company, candidate, application, and payment routes.
- `platform/plugins/job-board/routes/api.php`: Public and protected API v1 endpoints.
- `platform/plugins/job-board/routes/review.php`: Review-related routes.
- `platform/themes/jobbox/routes/web.php`: Theme AJAX routes and public theme registration.

## Admin Dashboard Flow

The admin dashboard is provided by Botble CMS ACL and dashboard modules.

- Admin URL is controlled by `ADMIN_DIR` in `.env`; default is `/admin`.
- Admin login is `/admin/login` when `ADMIN_DIR=admin`.
- Admin users are stored in the `users` table.
- Roles and permissions are stored in `roles` and `role_users`.
- Job board admin permissions are defined in `platform/plugins/job-board/config/permissions.php`.

Admin job-board sections include:

- Jobs
- Job applications
- Accounts
- Companies
- Packages
- Coupons
- Job categories, tags, skills, types, shifts, experiences, career levels, language levels, functional areas, degree types, and degree levels
- Reports
- Invoices
- Settings for job board, currencies, and invoice templates
- Import/export tools for jobs, accounts, and companies

## Applicant/User Flow

Applicant auth routes are defined in `platform/plugins/job-board/routes/account.php`.

Main applicant routes include:

- `/login`
- `/register`
- `/password/request`
- `/password/reset/{token}`
- `/account/overview`
- `/account/settings`
- `/account/security`
- `/account/applied-jobs`
- `/account/saved-jobs`
- AJAX upload endpoints for avatar, editor uploads, and resumes

Applicants are represented by `Botble\JobBoard\Models\Account` records with type `job-seeker`. Supporting profile tables include education, experience, languages, favorite skills, favorite tags, saved jobs, and activity logs.

## Employer/Company Flow

Employer account routes are also defined in `platform/plugins/job-board/routes/account.php`.

Main employer routes include:

- `/account/dashboard`
- `/account/employer/settings`
- `/account/companies`
- `/account/jobs`
- `/account/applicants`
- `/account/packages`
- `/account/invoices`

Employers are represented by `Botble\JobBoard\Models\Account` records with type `employer`. Companies are represented by `Botble\JobBoard\Models\Company` and related through `jb_companies_accounts`.

## Job Posting Flow

Jobs can be managed from:

- Admin dashboard: `platform/plugins/job-board/routes/web.php` under admin `job-board/jobs`.
- Employer dashboard: `platform/plugins/job-board/routes/account.php` under `/account/jobs`.

Important job code areas:

- `platform/plugins/job-board/src/Models/Job.php`
- `platform/plugins/job-board/src/Forms/JobForm.php`
- `platform/plugins/job-board/src/Forms/Fronts/JobForm.php`
- `platform/plugins/job-board/src/Http/Controllers/JobController.php`
- `platform/plugins/job-board/src/Http/Controllers/Fronts/AccountJobController.php`
- `platform/plugins/job-board/src/Events/EmployerPostedJobEvent.php`
- `platform/plugins/job-board/src/Events/AdminApprovedJobEvent.php`
- `platform/plugins/job-board/src/Events/JobPublishedEvent.php`

## Job Application Flow

Public job applications are handled by:

- `POST /jobs/apply/{id?}` mapped to `PublicController@postApplyJob`.
- Job application model: `platform/plugins/job-board/src/Models/JobApplication.php`.
- Request validation: `platform/plugins/job-board/src/Http/Requests/ApplyJobRequest.php`.
- Frontend forms: `InternalJobApplicationForm.php` and `ExternalJobApplicationForm.php`.
- Admin applications controller: `JobApplicationController.php`.
- Employer applicant management: `Fronts/ApplicantController.php`.
- Application events/listeners: `JobAppliedEvent`, `JobAppliedListener`, and `NewApplicationNotification`.

Applicants can track applications from `/account/applied-jobs`. Employers can manage applicants from `/account/applicants`.

## Authentication System

The project has separate admin and account-facing authentication concepts.

- Admin auth comes from Botble ACL and uses the `users` table.
- Front account auth comes from the job-board plugin and uses `jb_accounts`.
- Account type controls access to job seeker versus employer sections.
- Password reset support exists for both Laravel/Botble auth areas.
- Sanctum is installed for protected API endpoints under `/api/v1`.

## Email and Notification System

Laravel mail configuration is in `config/mail.php` and reads environment values such as `MAIL_MAILER`, `MAIL_HOST`, `MAIL_USERNAME`, and `MAIL_PASSWORD`.

Botble job-board email templates are declared in `platform/plugins/job-board/config/email.php`. Existing templates include:

- Admin new job application
- Employer new job application
- Job seeker applied job
- New job posted
- New company profile created
- Job expired soon
- Job renewed
- Payment receipt
- Account registered

Related events and listeners live in `platform/plugins/job-board/src/Events` and `platform/plugins/job-board/src/Listeners`.

## Database Configuration

Database configuration is environment-driven.

Important values in `.env`:

- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

Use `.env.example` as the template. The real `.env` file must not be committed.

The installed demo/current schema is available in `database.sql`. Laravel and plugin migrations also exist under:

- `database/migrations`
- `platform/core/*/database/migrations`
- `platform/packages/*/database/migrations`
- `platform/plugins/*/database/migrations`

## Important Database Tables

Important visible tables from `database.sql` include:

- Admin/auth: `users`, `roles`, `role_users`, `activations`, `password_reset_tokens`, `personal_access_tokens`
- Botble CMS: `settings`, `pages`, `posts`, `categories`, `tags`, `menus`, `menu_nodes`, `widgets`, `slugs`, `meta_boxes`, `media_files`, `media_folders`
- Job board: `jb_accounts`, `jb_companies`, `jb_jobs`, `jb_applications`, `jb_saved_jobs`, `jb_packages`, `jb_transactions`, `jb_invoices`, `jb_invoice_items`, `jb_reviews`
- Job metadata: `jb_categories`, `jb_tags`, `jb_job_skills`, `jb_job_types`, `jb_job_shifts`, `jb_job_experiences`, `jb_career_levels`, `jb_language_levels`, `jb_functional_areas`, `jb_degree_types`, `jb_degree_levels`
- Relationships/translations: `jb_jobs_categories`, `jb_jobs_skills`, `jb_jobs_tags`, `jb_jobs_types`, and multiple `*_translations` tables
- Supporting modules: `countries`, `states`, `cities`, `payments`, `payment_logs`, `newsletters`, `contacts`, `faqs`, `galleries`, `testimonials`

## Upload and File Handling

Botble media handling is provided by `platform/core/media`.

Runtime/demo upload files are currently located under `public/storage`. This folder is ignored because it contains generated/uploaded media, not source code. The current demo database references paths such as `/storage/...`, so deleting the local folder can break demo images and resumes.

For deployment, run:

```bash
php artisan storage:link
php artisan cms:publish:assets
```

Then upload or restore media as needed.

## Third-Party Packages and Integrations

Composer dependencies include Laravel, Laravel Sanctum, Laravel Socialite, Botble packages, Predis, AWS SDK, Google API libraries, Stripe, PayPal, Razorpay, DataTables, Laravel Excel, DomPDF, mPDF, and related support packages.

Installed plugins include payment integrations for PayPal, Paystack, Razorpay, SSLCommerz, and Stripe. Configure real payment credentials only through environment variables or the admin settings system, never directly in source files.

## How to Run Locally

1. Install PHP 8.2 or 8.3, Composer, MySQL/MariaDB, and Node.js/npm.
2. Copy `.env.example` to `.env`.
3. Set `APP_URL`, database credentials, mail credentials, and any required service keys.
4. Run `composer install`.
5. Run `php artisan key:generate`.
6. Create the database and either import `database.sql` or run the migrations/seeders appropriate to the install path.
7. Run `php artisan storage:link`.
8. Run `php artisan cms:publish:assets`.
9. Run `npm install` and `npm run prod` if frontend assets need rebuilding.
10. Start locally with `php artisan serve`.

## Deployment Notes

- Point the web server document root to `public/`.
- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Generate a unique `APP_KEY`.
- Change the default admin URL by setting `ADMIN_DIR`.
- Configure queue, mail, cache, session, and payment settings for the target environment.
- Do not deploy `.env`, logs, cache files, local uploads, or development dependencies as committed source.

## Known Issues and Unclear Areas

- The app is still branded internally as JobBox/Jobbox in theme names, package names, settings, and demo content.
- Demo credentials and demo data exist in `database.sql`; they must be changed before production.
- `public/storage` contains local demo/upload media and is ignored from Git.
- `public/vendor` and `public/themes` are generated/published assets and are ignored; regenerate them during setup.
- No new JobRango-specific features have been added yet.
- Test coverage was not expanded during this cleanup.
