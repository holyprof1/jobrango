# Admin Approval Settings Report

## Implemented

### Quick Moderation Actions

Admin job list now exposes direct actions for:

- `View`
- `Approve`
- `Reject`
- `Edit`

These actions are available without opening each job first.

### Approval Settings

Existing setting already used:

- `job_board_enable_post_approval`

New setting added:

- `job_board_verified_company_auto_approval`

## Approval Logic

### If post approval is OFF

- jobs auto-approve and publish as before

### If post approval is ON

- jobs require moderation by default
- if `verified_company_auto_approval` is enabled, jobs from published and verified companies auto-approve

## Safe Behavior

- Verified-company auto-approval defaults to off.
- This keeps moderation strict unless an admin explicitly enables the faster path.

## Validation

- Admin moderation routes exist for:
  - `/admin/job-board/jobs/{job}/view`
  - `/admin/job-board/jobs/{job}/approve`
  - `/admin/job-board/jobs/{job}/reject`
