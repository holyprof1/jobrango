# JobRango Demo Content Report

This report summarizes demo content found in `database.sql` and the seeded data files. The bundled database dump is useful for a first test run, but it includes sample data that should be removed or replaced before launch.

## Summary

`database.sql` contains an installed Botble CMS JobBox demo state with JobRango branding updates, sample job-board records, sample CMS pages, sample blog content, sample media, and demo credentials.

For the first usable local or staging test, import `database.sql`. For production, treat it as starter/demo data only.

## Demo Credentials

Admin demo account:

```text
Admin URL: /admin/login
Username: admin
Password: 12345678
Email in dump: fhowell@corwin.com
```

The admin password must be changed immediately after setup, or the demo admin account should be replaced with a real administrator account.

Job-board front accounts are also seeded with the demo password:

```text
Password: 12345678
Example employer: employer@archielite.com
Example job seeker: job_seeker@archielite.com
```

These accounts are for testing only and should be deleted before launch.

## Demo Data Counts

Approximate counts found in `database.sql`:

- Admin users: 1
- Job-board accounts: 100
- Companies: 20
- Jobs: 51
- Job applications: 20
- CMS pages: 18
- Blog posts: 3
- Blog categories: 7
- Job categories: 10
- Countries: 6
- States: 6
- Cities: 6
- Media records: 161
- Widgets: 17
- Settings rows: 50

## Demo Users

The dump includes one backend admin user and many front-office job-board accounts. The seeded front accounts use fake names, sample emails, avatars, candidate profiles, and employer profiles.

Recommendation:

- Create a real admin user in `/admin/system/users`.
- Change or remove the demo backend admin account.
- Remove sample applicants and employers through the Job Board admin screens where available.
- Avoid direct database deletion unless you are doing a controlled bulk cleanup on a backed-up staging database.

## Demo Companies

The dump includes 20 sample companies with logos, profile details, addresses, and related jobs.

Recommendation:

- Delete demo companies through the admin dashboard after deleting or reassigning related jobs.
- Replace company logos and contact details with real data only after the launch content plan is ready.

## Demo Jobs

The dump includes 51 sample jobs with categories, locations, skills, salary information, and application data.

Recommendation:

- Use admin job management to remove demo jobs.
- Review job categories, job types, job shifts, skills, career levels, degree types, and degree levels before creating real listings.

## Demo Applications

The dump includes 20 sample job applications.

Recommendation:

- Delete demo applications before deleting related demo jobs or accounts.
- Confirm the application workflow after cleanup by submitting a fresh test application.

## Demo Pages

The dump includes 18 CMS pages, including multiple homepage variants, Jobs, Companies, Candidates, About us, Pricing Plan, Contact, Blog, Cookie Policy, FAQs, Services, Terms, and Job Categories.

Some pages contain placeholder or imported demo text.

Recommendation:

- Keep only the pages needed for JobRango's launch structure.
- Edit pages through `/admin/pages`.
- Update menu links and homepage selection after removing unused page variants.

## Demo Blog Posts and Categories

The dump includes 3 sample blog posts and 7 blog categories. The posts are job/interview themed but still behave as demo content.

Recommendation:

- Delete or rewrite demo posts before launch.
- Replace blog categories with the editorial categories JobRango will actually use.

## Demo Categories and Locations

The dump includes sample job categories and a small sample location set.

Recommendation:

- Review job categories before launch.
- Replace or expand locations based on JobRango's target market.
- Use admin screens where available so slugs, metadata, and related records remain consistent.

## Demo Media

The dump includes 161 media records. Common demo media paths include:

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
- `locations/*`
- `job-categories/*`

Current package verification:

- The bundled script package currently includes the demo media files on disk under `public/storage`.
- Local verification found 161 `media_files` database rows and those records resolved to existing files.
- This package does not currently depend on `storage/app/public` being populated for the bundled demo media to display.

Recommendation:

- Replace logo and favicon from Botble admin.
- Remove unused demo media from `/admin/media` after related content is removed.
- Do not delete media files directly from disk until content relationships are reviewed.
- If a future deployment package is missing `public/storage` media, restore that media separately before assuming the database dump is wrong.

## Demo Branding Still Likely to Appear

The rebranding phase updated safe visible JobBox defaults to JobRango, but imported demo content can still show:

- Sample names and fake profile data.
- `archielite.com` test emails.
- Placeholder page and post body text.
- Demo menu items such as multiple homepage variants.
- Demo images and documents.

These are content records rather than application source code and should be cleaned through the admin dashboard.

## Manual Admin Cleanup vs Database Cleanup

Prefer admin cleanup for:

- Pages
- Blog posts and categories
- Jobs
- Companies
- Applicants and employers
- Applications
- Menus
- Widgets
- Media
- Theme options

Database cleanup can be used only for:

- Fresh staging resets.
- Scripted cleanup after a full backup.
- One-time controlled removal where related tables are understood.

Botble uses slugs, metadata, media records, settings, and relationship tables, so direct deletes can leave orphan records if they are not handled carefully.
