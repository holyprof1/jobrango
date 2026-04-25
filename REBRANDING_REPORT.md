# JobRango Rebranding Report

## Scope

This pass starts the rebranding/setup phase only. It updates safe user-facing defaults and documentation, without adding major features, editing vendor files, renaming Botble folders, changing PHP namespaces, changing composer package identifiers, or changing database table names.

## Files Changed

- `README.md`: Added project setup summary and key admin paths.
- `PROJECT_OVERVIEW.md`: Updated the status from cleanup-only to first rebranding pass.
- `CLEANUP_REPORT.md`: Updated next-step wording now that visible branding work has started.
- `REBRANDING_REPORT.md`: Added this report.
- `.env.example`: Updated default mail sender address placeholder to JobRango.
- `config/app.php`: Updated fallback app name to JobRango.
- `config/mail.php`: Updated fallback mail sender name to JobRango.
- `database.sql`: Updated obvious demo/site branding values for fresh imports.
- `database/seeders/ThemeOptionSeeder.php`: Updated site title, SEO description, copyright, and contact email seed defaults.
- `database/seeders/WidgetSeeder.php`: Updated newsletter title and footer introduction seed defaults.
- `database/seeders/FaqSeeder.php`: Updated FAQ questions that referenced JobBox.
- `database/seeders/PageSeeder.php`: Updated homepage/job-page search box copy and contact company/email seed defaults.
- `platform/themes/jobbox/theme.json`: Updated visible theme name/description/author while keeping the namespace stable.
- `platform/themes/jobbox/functions/theme-options.php`: Updated default footer copyright, introduction, and app-advertisement text.
- `platform/themes/jobbox/partials/shortcodes/job-grid.blade.php`: Updated image alt text from JobBox to JobRango.
- `platform/themes/jobbox/assets/sass/style.scss` and `platform/themes/jobbox/assets/sass/pages/_pages.scss`: Updated source comments.
- `platform/themes/jobbox/lang/*.json`: Updated the legacy footer-introduction translation string to JobRango text.

## Branding Strings Replaced

- Site name defaults now use `JobRango`.
- Tagline/default headline uses `Find your range of work.`
- Short description uses `JobRango is a job platform where companies post work opportunities and applicants apply through a simple user portal.`
- Default copyright uses `Copyright (c) <year> JobRango. All rights reserved.`
- Placeholder contact email uses `contact@jobrango.example` or `noreply@jobrango.example`.
- FAQ text `How JobBox Work?` now reads `How JobRango works?`

## Intentionally Not Changed

- `platform/themes/jobbox/` remains unchanged because it is the active Botble theme folder.
- `Theme\Jobbox` namespaces, `JobboxController`, and related route/controller references remain unchanged because renaming them would require a coordinated theme refactor.
- Database keys such as `theme-jobbox-logo`, `theme-jobbox-favicon`, and the active theme value `jobbox` remain unchanged because Botble uses the theme identifier for settings lookup.
- Historical cleanup notes that mention removed `Jobbox...zip` files remain in `CLEANUP_REPORT.md` because they document what was deleted.
- Unrelated upstream plugin metadata such as `platform/plugins/team/plugin.json` was not changed because it is vendor/plugin metadata, not JobRango user-facing branding.
- Generic demo contacts, jobs, locations, companies, blog posts, and sample page bodies were not rewritten in this pass.

## Logo And Favicon

Current configured media references:

- Frontend logo setting: `theme-jobbox-logo`, currently `general/logo.png`.
- Frontend favicon setting: `theme-jobbox-favicon`, currently `general/favicon.png`.
- Admin logo setting: `admin_logo`, currently `general/logo-light.png`.
- Admin favicon setting: `admin_favicon`, currently `general/favicon.png`.
- Contact/company placeholder logo: `general/logo-company.png`.

Final logo/favicon were not generated in this pass. Upload final assets through `/admin/media`, then assign them in `/admin/theme/options`. Admin logo/favicon settings are stored in the `settings` table as `admin_logo` and `admin_favicon`.

## Email Branding

Email template locations:

- Base email layout: `platform/core/base/resources/email-templates/header.tpl` and `footer.tpl`.
- Admin password reminder: `platform/core/acl/resources/email-templates/password-reminder.tpl`.
- Job-board emails: `platform/plugins/job-board/resources/email-templates/*.tpl`.
- Newsletter/contact emails: plugin-specific `resources/email-templates` folders.

No hard-coded JobBox branding was found in the job-board email template files. Email templates use variables such as `{{ site_title }}`, `{{ site_logo }}`, and `{{ site_copyright }}`.

Configure SMTP and sender identity in `.env` using `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, and `MAIL_FROM_NAME`. The same settings can be managed in `/admin/settings/email`. Email template logo, copyright, contact email, and template bodies can be managed in `/admin/settings/email/templates`.

## Admin Settings Locations

- Site name and general app settings: `/admin/settings/general`
- Theme site title, SEO description, copyright, logo, favicon, social links, and homepage selection: `/admin/theme/options`
- Homepage and static page content: `/admin/pages`
- Footer introduction and newsletter widgets: `/admin/widgets`
- Media uploads for logo/favicon: `/admin/media`
- Mail transport and sender settings: `/admin/settings/email`
- Email template branding and body content: `/admin/settings/email/templates`
- Job-board behavior such as registration/application settings: `/admin/job-board/settings/general`

## Database And Demo Content Notes

`database.sql` was updated for obvious site/demo branding values in:

- The SQL database comment.
- `settings` rows for site title, SEO description, copyright, and contact email.
- `pages` rows for homepage/job-page hero copy and contact company/email.
- `widgets` rows for newsletter and footer introduction.
- `faqs` rows that referenced JobBox.

If a database has already been imported, these source-file changes will not automatically update that running database. Re-import the updated dump, run seeders in a controlled environment, or change the values from the Botble admin dashboard.

## Risks And Follow-Up Items

- Existing imported databases may still contain old branding until admin settings/content are updated.
- Final production logo/favicon files still need to be designed and uploaded.
- Demo contacts, blog posts, fake companies, sample resumes, and filler page bodies still need a content cleanup pass.
- Internal `jobbox` identifiers should only be renamed in a dedicated compatibility-tested theme refactor.
- Production mail, payment, OAuth, analytics, and storage credentials must remain outside Git in `.env` or secure admin settings.
