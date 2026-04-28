# Company Flow Report

## Goal

Make company management fast enough for day-to-day hiring while keeping admin controls available.

## Implemented

### Auto Verification

- New companies now default to verified through `job_board_auto_verify_new_companies`.
- The setting defaults to `true` when not explicitly set.
- Employer-created companies are created as:
  - `status = published`
  - `is_verified = true`
- Admin-created companies default to verified in the form and in AJAX create flows.
- Admins can still unverify companies later from:
  - admin company table quick toggle
  - admin company detail page
  - admin company edit form

### Homepage Selection

- Homepage company sections now use the company homepage flag consistently.
- Admin company form label is now `Show on homepage`.
- Admin company table now has a direct `Homepage` toggle.
- Homepage sections only pull published companies marked for homepage display.

### Admin Company Management

- Admin company table now shows:
  - database `ID`
  - logo
  - company name
  - short display ID in `JR-COMP-{sequence}` format
  - `Verified` quick toggle
  - `Homepage` quick toggle
  - created date
  - status
  - view/edit/delete actions
- Long internal unique IDs are no longer shown in the main admin list.
- Internal `unique_id` and `tax_id` remain available in the admin form under `Advanced`.

### Admin Company Edit Form

- Admin form supports:
  - company name
  - description
  - full content/about
  - email
  - phone
  - website
  - address
  - postal code
  - location selector
  - year founded
  - number of offices
  - number of employees
  - annual revenue
  - CEO/contact person
  - published/draft status
  - verified yes/no
  - show on homepage yes/no
  - logo
  - cover image
  - social links
- Internal fields are now grouped more cleanly:
  - `tax_id`
  - `unique_id`

### Employer Company Flow

- Employer company create/edit now keeps the form focused on usable profile fields:
  - logo
  - cover image
  - company name
  - short description
  - about/company content
  - contact email
  - phone
  - website
  - CEO/contact person
  - year founded
  - number of offices
  - number of employees
  - annual revenue
  - location
  - address
  - postal code
  - social links
- Advanced/internal fields are no longer part of the employer flow:
  - unique ID
  - status
  - verified flag
  - homepage flag
  - latitude
  - longitude
  - internal moderation controls
- Employer company save now only accepts a safe allowlist of company fields.
- Employer company create no longer wipes `logo` and `cover_image` values before save.

### Public Company Page

- Public company page keeps the verified badge when available.
- Public company page no longer uses the generic homepage hero if the company cover matches the homepage banner asset.
- Contact CTA now prefers:
  - phone
  - email fallback
- Recent jobs section is labeled more clearly.

## Job Posting Behavior

- Verified company job auto-approval now defaults to enabled through:
  - `job_board_verified_company_auto_approval = true` by default
- Approval behavior is now:
  - if post approval is off: jobs publish immediately
  - if post approval is on: verified + published companies auto-approve
  - if post approval is on and company is not verified/published: moderation still applies

## Routes Updated

- `POST /admin/job-board/companies/{company}/toggle-verification`
- `POST /admin/job-board/companies/{company}/toggle-homepage`

## Files Changed

- `platform/plugins/job-board/src/Supports/JobBoardHelper.php`
- `platform/plugins/job-board/src/Models/Company.php`
- `platform/plugins/job-board/src/Forms/CompanyForm.php`
- `platform/plugins/job-board/src/Forms/Fronts/CompanyForm.php`
- `platform/plugins/job-board/src/Forms/Settings/GeneralSettingForm.php`
- `platform/plugins/job-board/src/Http/Controllers/CompanyController.php`
- `platform/plugins/job-board/src/Http/Controllers/Fronts/CompanyController.php`
- `platform/plugins/job-board/src/Http/Requests/CompanyRequest.php`
- `platform/plugins/job-board/src/Http/Requests/AccountCompanyRequest.php`
- `platform/plugins/job-board/src/Http/Requests/Settings/GeneralSettingRequest.php`
- `platform/plugins/job-board/src/Tables/CompanyTable.php`
- `platform/plugins/job-board/routes/web.php`
- `platform/plugins/job-board/resources/lang/en/settings.php`
- `platform/plugins/job-board/resources/views/companies/partials/advanced-fields.blade.php`
- `platform/plugins/job-board/resources/views/companies/partials/table-toggle.blade.php`
- `platform/themes/jobbox/functions/shortcodes.php`
- `platform/themes/jobbox/views/job-board/company.blade.php`

## Later

- Decide whether admin company quick toggles should use inline AJAX instead of form-submit refresh.
- Decide whether `job_board_auto_verify_new_companies` should fully replace the older inverted `verify_account_created_company` setting in all locales and docs.
- Decide whether company cover fallback detection should expand beyond the current homepage banner asset check.
