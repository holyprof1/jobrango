# Employer Flow Report

## Company Flow Changes

### Employer Company Create/Edit

Employer-side company management is now focused on practical profile data instead of moderation/internal controls.

Visible employer company fields now include:

- logo
- cover image
- company name
- description
- company about/content
- email
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

Hidden from employer flow:

- unique ID
- homepage flag
- verified flag
- moderation status
- latitude
- longitude

### Company Save Behavior

- Employer company save now uses an allowlist of safe fields before persistence.
- Employer company create no longer resets logo and cover image to `null`.
- New employer companies auto-verify and publish by default through the new company verification setting.

### Job Posting Flow

- `/account/jobs/create` remains available to employers with posting access.
- Verified + published companies now qualify for immediate job publishing when verified-company auto-approval is enabled.
- This keeps the employer posting flow immediate without removing admin moderation settings.

## Routes Confirmed

- `/account/companies`
- `/account/companies/edit/{company}`
- `/account/jobs/create`

## Smoke Validation

- Demo employer credentials validated successfully.
- Browser route checks passed for:
  - `/account/companies`
  - `/account/companies/edit/21`
  - `/account/jobs/create`

## Later

- Add a dedicated employer company card/list redesign if the current table still feels too admin-like.
- Decide whether annual revenue should stay in the default employer form or move to an optional advanced panel.
