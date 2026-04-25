# JobRango Launch Checklist

Use this checklist before moving JobRango from local or staging into production.

## Access and Security

- [ ] Change the demo admin password.
- [ ] Create real administrator accounts.
- [ ] Delete or disable the demo admin account if no longer needed.
- [ ] Remove demo job-seeker and employer accounts.
- [ ] Confirm `.env` is not committed.
- [ ] Confirm `APP_ENV=production`.
- [ ] Confirm `APP_DEBUG=false`.
- [ ] Generate and keep a secure `APP_KEY`.
- [ ] Set a non-default `ADMIN_DIR` if desired.
- [ ] Review admin user roles and permissions.
- [ ] Review file upload limits and allowed file types.
- [ ] Run a basic security review before launch.

## Environment

- [ ] Configure production `APP_URL`.
- [ ] Configure database host, database name, username, and password.
- [ ] Configure cache, session, and queue drivers.
- [ ] Configure domain DNS.
- [ ] Install and verify SSL.
- [ ] Point the web server document root to `public`.
- [ ] Confirm PHP version is compatible with `^8.2|^8.3`.
- [ ] Confirm required PHP extensions are installed.
- [ ] Set writable permissions for `storage` and `bootstrap/cache`.
- [ ] Run `php artisan storage:link`.
- [ ] Run `php artisan cms:publish:assets`.
- [ ] Run cache optimization commands after final environment changes.

## Content Cleanup

- [ ] Remove demo jobs.
- [ ] Remove demo companies.
- [ ] Remove demo applications.
- [ ] Remove demo users and candidate profiles.
- [ ] Review and replace demo pages.
- [ ] Review and replace demo blog posts.
- [ ] Review and replace demo blog categories.
- [ ] Review and replace demo job categories.
- [ ] Review and replace demo job types, shifts, skills, career levels, and degree levels.
- [ ] Review and replace demo locations.
- [ ] Remove unused demo media files.
- [ ] Clean menu items such as unused homepage variants.
- [ ] Clean widgets and footer links.

## Branding and SEO

- [ ] Upload final JobRango logo.
- [ ] Upload final favicon.
- [ ] Upload admin logo if needed.
- [ ] Upload email template logo.
- [ ] Configure site title and tagline.
- [ ] Configure homepage SEO title and meta description.
- [ ] Configure default Open Graph image.
- [ ] Review footer copyright text.
- [ ] Review all email template sender names and footer text.

## Job Board Configuration

- [ ] Configure job categories.
- [ ] Configure job types.
- [ ] Configure job skills.
- [ ] Configure job shifts.
- [ ] Configure salary display rules.
- [ ] Configure company profile requirements.
- [ ] Configure application requirements.
- [ ] Review packages or paid posting settings if enabled.
- [ ] Review moderation workflow for jobs, companies, and applications.

## Mail and Notifications

- [ ] Configure SMTP or another production mail driver.
- [ ] Configure mail from address.
- [ ] Configure mail from name.
- [ ] Send a test email from Botble admin.
- [ ] Test registration emails.
- [ ] Test password reset emails.
- [ ] Test job application notifications.
- [ ] Test employer notifications.
- [ ] Review all public-facing email templates.

## Workflow Testing

- [ ] Test applicant registration.
- [ ] Test applicant login.
- [ ] Test applicant profile update.
- [ ] Test CV or resume upload.
- [ ] Test employer registration.
- [ ] Test employer/company profile update.
- [ ] Test employer job posting.
- [ ] Test applicant job application.
- [ ] Test applicant application tracking.
- [ ] Test admin job moderation.
- [ ] Test admin company moderation.
- [ ] Test search and filters.
- [ ] Test mobile layout.

## Operations

- [ ] Back up the production database before launch.
- [ ] Back up uploaded media.
- [ ] Confirm scheduled backups.
- [ ] Confirm server logs are available.
- [ ] Confirm error monitoring if used.
- [ ] Confirm queue worker monitoring if queues are enabled.
- [ ] Confirm a rollback plan.

