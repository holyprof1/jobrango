# Account Dashboard Cleanup Report

## Overview error fix

- `/account/overview` was failing because `jb_saved_jobs` did not include timestamps while account flows expected recency-aware saved-job ordering.
- Fix applied:
  - added a safe migration to add nullable `created_at` and `updated_at` to `jb_saved_jobs`
  - backfilled existing rows with current timestamps
  - enabled `withTimestamps()` on the saved-jobs many-to-many relation
  - changed overview saved-job ordering to prefer `jb_saved_jobs.created_at` and then `jb_jobs.created_at`

## Dashboard cleanup

- Kept the compact profile header and improved it with:
  - initials fallback when no avatar is uploaded
  - `Edit Profile`, `Browse Jobs`, and `Preview Profile` actions
- Retained and polished the overview stat cards:
  - Applied Jobs
  - Saved Jobs
  - Profile Completion
  - Recent Applications
- Added an explicit `Recommended Jobs` panel instead of only showing suggestions as an empty-state fallback.
- Reduced the risk of awkward sidebar active states by switching to route-based matching.

## Account page consistency

- Refreshed these job seeker pages to use the same dashboard shell/panel language:
  - `/account/overview`
  - `/account/settings`
  - `/account/security`
  - `/account/jobs/applied-jobs`
  - `/account/saved-jobs`
  - `/account/educations`
  - `/account/experiences`
- Resume/CV remains managed through `/account/settings` in the current frontend flow.

## Extra fixes

- Fixed applied-jobs sorting so `oldest` now actually sorts oldest first.
- Saved jobs now have cleaner inline remove behavior and mobile-safe filters.
