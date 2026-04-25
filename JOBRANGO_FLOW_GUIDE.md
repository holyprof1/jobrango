# JobRango Flow Guide

## What JobRango Is

JobRango is a job marketplace built on Botble JobBoard/JobBox. Its core purpose is to connect employers, companies, and talent finders with job seekers and applicants.

At a high level, the platform already supports:

- public job discovery
- company listings
- candidate/talent listings
- employer accounts and job posting
- applicant accounts and job applications
- admin moderation and configuration

## Main Roles

### 1. Admin

The admin manages the whole platform from the Botble admin panel at the configured admin URL.

Admin responsibilities include:

- logging into the dashboard
- managing admin users and permissions
- managing job seeker and employer accounts
- managing companies
- managing jobs
- reviewing job applications
- configuring settings, theme options, menus, media, email, languages, and plugins
- moderating what is visible on the public site

### 2. Employer / Company / Talent Finder

This is the hiring-side user. In Botble JobBoard terms, this is the employer account type.

This role can:

- register or log in on the frontend
- choose/register as an employer account
- create and manage company profiles
- post jobs
- view applicants for their jobs
- access the employer dashboard
- browse candidate profiles if candidate visibility settings allow it

### 3. Job Seeker / Applicant

This is the applicant-side user.

This role can:

- register or log in on the frontend
- keep a candidate/applicant profile
- upload CV/resume and profile details
- browse jobs and companies
- save jobs
- apply for jobs
- review applied jobs from the account area

## Admin Flow

The admin flow is:

1. Open `/admin/login`.
2. Sign in with an admin account.
3. Land on the admin dashboard.
4. Use admin menus to manage:
   - users and permissions
   - employers/accounts
   - companies
   - jobs
   - job applications
   - settings, menus, appearance, theme options, media, email, plugins

Job applications are managed in the admin through the Job Board module and related permissions such as `job-applications.index`.

## Employer / Talent Finder Flow

The hiring-side flow is:

1. Open `/register`.
2. Register as an employer account.
   The theme register flow supports employer registration through the `is_employer` option when that setting is enabled.
3. Log in and enter the employer dashboard.
4. Create a company profile if needed.
5. Post a job from the employer account area.
6. Review incoming applicants from the employer dashboard/applicants area.
7. Manage jobs, company information, and account settings over time.

Relevant frontend account routes already exist for:

- dashboard
- company management
- job creation/editing
- applicant review/download CV
- employer settings

## Job Seeker Flow

The applicant flow is:

1. Open `/register`.
2. Register as a normal job seeker/applicant account.
3. Log in to the account area.
4. Complete the profile, resume/CV, education, experience, and other details if needed.
5. Browse `/jobs`, job categories, locations, and companies.
6. Save jobs or apply directly.
7. Review saved jobs and applied jobs in the account area.

The current theme/account area already includes routes for:

- account overview
- settings/security
- saved jobs
- applied jobs
- resume/CV download handling
- education, experience, languages, and related profile data

## Job Posting Flow

Who can post jobs now:

- employer accounts on the frontend
- admins from the backend

Job posting uses existing Botble JobBoard structures such as:

- company
- category
- job type
- salary / salary range
- location
- skills / tags
- job content and requirements

Jobs can be managed from both:

- frontend employer dashboard
- backend admin panel

Approval/moderation:

- Botble already supports admin-managed job records and publication states.
- Before launch, moderation expectations should be reviewed in Job Board settings and admin workflow so employers do not publish unreviewed content unintentionally.

## Application Flow

How applications work now:

1. A job seeker opens a public job page.
2. The user applies through the job application flow.
3. The platform stores the application in `job_applications`.
4. Employers can review applicants from the frontend account/applicants area.
5. Admins can review applications from the backend Job Board area.

Application data supported by the current script includes items such as:

- applicant identity details
- email
- phone
- cover letter / summary
- CV or resume file when provided

Email notifications:

- Botble JobBoard includes employer/admin email templates for new job applications and new registrations.
- These should be reviewed and branded before production.

## Talent Finder Focus

JobRango can already support more than simple vacancy posting.

Existing talent-finder features:

- candidate listing pages
- candidate profile pages
- CV download flow
- candidate visibility/privacy settings
- settings to hide candidate information from guests
- settings to limit candidate details to employers only

What this means:

- employers can use the platform both to post roles and to browse talent profiles
- candidate search/discovery exists, but it still needs product and content refinement to feel fully JobRango-specific

Recommended next-step customization:

- refine the candidate listing page copy and filters
- decide whether the candidate directory should stay public, members-only, or employer-only
- decide whether JobRango wants to position this as “Browse Talent” or “Talent Finder”

## What Exists Now vs. What Still Needs Customization

### Already in the Botble JobBox base

- admin panel
- employer and applicant account types
- jobs, companies, candidates, and applications
- menus, pages, theme options, media, and email templates
- frontend account dashboards

### Still showing demo/template behavior before customization

- some seeded demo pages and generic marketing copy
- blog/news sections in seeded content
- quick-start wizard/demo onboarding in admin
- default admin credentials on this local install
- default admin path `/admin`

### Recommended JobRango-specific follow-ups

- finish replacing remaining demo copy across static pages
- brand the candidate/talent discovery experience
- review email templates and notification language
- decide public vs restricted talent browsing rules
- change default admin credentials and admin path before production
- activate a valid Botble license before production
