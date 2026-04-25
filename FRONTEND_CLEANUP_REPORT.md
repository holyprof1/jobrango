# Frontend Cleanup Report

## Summary

- Homepage and listing job cards remain compact and no longer render long descriptions.
- Auth, newsletter, and footer surfaces remain free of the oversized broken demo imagery.
- The job seeker account overview now renders as a compact dashboard instead of the broken oversized profile layout.
- The default `/jobs` page shows active demo jobs again after refreshing expired local demo dates.

## Job Card Changes

- Removed description/excerpt output from grid, list, company, and homepage job-card partials.
- Reduced visible category chips to a maximum of 2.
- Improved badge spacing and wrapping so the job-type badge no longer overlaps the company/title area.
- Kept the company logo/initial badge treatment so the large placeholder image does not return.
- Default listing behavior now stays focused on active jobs instead of making closed states the primary card action.

## Jobs Listing Fix

- The earlier `No Jobs` state on `/jobs` was caused by demo jobs still having 2025 `expire_date` and `application_closing_date` values while the local environment date is in 2026.
- Category counts are generated from active jobs, so once demo dates were stale the page could feel inconsistent.
- A local migration now refreshes stale published demo jobs into a current open window so the first page of `/jobs` loads visible roles again.

## Account Overview Cleanup

- Replaced the large broken top banner on `/account/overview` with a compact profile header card.
- Fixed the name/location/summary overlap by moving the account header into a dedicated dashboard shell.
- Added overview stat cards for applied jobs, saved jobs, profile completion, and recent applications.
- Added recent applications, saved jobs/recommended jobs, and profile tips panels without introducing heavy new product features.
- Sidebar navigation now includes `My Profile`, `Overview`, `Applied Jobs`, `Saved Jobs`, `CV / Resume`, `Security`, `Experiences`, and `Educations`.

## Footer / Auth / Newsletter

- Footer content remains JobRango-specific rather than JobBox demo copy.
- Footer navigation remains aligned to job seeker, employer, and company workflows.
- Auth screens remain form-first with no large illustration panel.
- Newsletter keeps the cleaner text-led block without the broken side images.

## Remaining Frontend Artifacts

- Optional blog/candidate/plugin-driven features still exist if their modules remain active.
- Real uploaded company/candidate cover media can still display where present; only the repeated default fallback treatment was reduced.
- The real Botble license reminder is an admin concern and was not removed.
