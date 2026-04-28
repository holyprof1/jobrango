# Admin Approval Settings Report

## Company Verification Settings

New/default behavior:

- `job_board_auto_verify_new_companies`
  - default: `true`
  - effect: new companies created by employers or admins start verified

- `job_board_verified_company_auto_approval`
  - default: `true`
  - effect: when job post approval is enabled, published + verified companies can still auto-publish jobs

Existing moderation setting remains:

- `job_board_enable_post_approval`

## Company Approval Logic

### New Companies

- Employer-created companies:
  - publish immediately
  - verify automatically by default

- Admin-created companies:
  - default to verified
  - can still be saved with a different status if the admin changes it

### Jobs From Companies

- If `job_board_enable_post_approval` is off:
  - jobs publish immediately

- If `job_board_enable_post_approval` is on:
  - published + verified companies auto-approve jobs when `job_board_verified_company_auto_approval` is on
  - all other jobs remain pending moderation

## Admin Quick Actions

Admin company list now exposes direct row-level controls for:

- `Verified` toggle
- `Homepage` toggle
- `View`
- `Edit`
- `Delete`

These controls work without opening the company first.

## Routes and Controllers

- `POST /admin/job-board/companies/{company}/toggle-verification`
- `POST /admin/job-board/companies/{company}/toggle-homepage`
- `platform/plugins/job-board/src/Http/Controllers/CompanyController.php`
- `platform/plugins/job-board/src/Tables/CompanyTable.php`

## Validation

- Route checks passed for:
  - `/admin/job-board/companies`
  - admin company edit page
  - admin company detail/view page
- Browser smoke test confirmed:
  - verify toggle changed state and restored correctly
  - homepage toggle changed state and restored correctly
