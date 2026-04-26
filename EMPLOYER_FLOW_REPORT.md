# Employer Flow Report

## What Changed

### Employer Header State

- Employer header now shows:
  - `Home`
  - `Jobs`
  - `Companies`
  - `Employer Dashboard`
  - `Post a Job`
  - `Applicants`
  - company/profile dropdown
  - logout

### Employer Dashboard Sidebar

- Reworked the sidebar into a compact profile card plus tighter menu stack.
- Menu order now matches the requested product flow more closely:
  - Dashboard
  - Jobs
  - Companies
  - Applicants
  - Reviews
  - Settings

### Employer Jobs Flow

- `/account/jobs` now renders as a product-style jobs management page instead of a generic table.
- Each employer job card now shows:
  - readable display ID
  - company context
  - status and moderation badges
  - applicant counts
  - direct actions for `View Job`, `Edit`, `Applicants`, and `Application Form`

### Posting Flow

- Job create still uses the existing form engine, but the visible flow stays focused on normal hiring inputs.
- After save, the flow now sends employers to:
  - `/account/jobs/{job}/application-form`

### Applicants Flow

- `/account/applicants` now defaults to jobs-first context instead of mixing all applicants together.
- Employers can open applicants filtered to a single job using:
  - `/account/applicants?job_id={job}`

## Custom Application Form Status

Implemented now:

- job-level `application_mode`
- job-level `application_form_schema`
- job-level `application_form_settings`
- post-save setup screen
- basic/custom mode selection
- screening-setting toggles

Not implemented yet:

- real drag/drop or add-question builder
- saved question authoring UI
- custom candidate-side rendering per job
- knockout scoring logic

## Smoke Validation

- Demo employer credentials validated successfully.
- Employer-owned jobs query works.
- Sample employer job routes resolved for:
  - application-form setup
  - applicants filtered by job
  - admin approval action
