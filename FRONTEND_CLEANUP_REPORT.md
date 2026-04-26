# Frontend Cleanup Report

## Summary

- Homepage and listing job cards remain compact, clickable, and no longer render long descriptions.
- Auth, newsletter, and footer surfaces remain free of the oversized broken demo imagery.
- The job seeker account overview now renders as a compact dashboard instead of the broken oversized profile layout.
- The default `/jobs` page shows active demo jobs again after refreshing expired local demo dates.
- The employer posting flow now opens with a simpler form that defaults salary currency to `NGN` and limits visible choices to `NGN` or `USD`.
- The account pages now share a cleaner dashboard shell with route-aware sidebar states and tighter mobile-safe layout spacing.

## Job Card Changes

- Removed description/excerpt output from grid, list, company, and homepage job-card partials.
- Reduced visible category chips to a maximum of 2.
- Rebuilt the card header so the green job-type badge sits in its own top-right pill instead of colliding with company/title text.
- Made the full card clickable to open the job detail page while keeping a clear `View Job` CTA.
- Added a secondary `Apply` CTA on the public list/grid cards when the job supports direct applications.
- Kept the company logo/initial badge treatment so the large placeholder image does not return.
- Closed jobs no longer show a large closed-state button as the main action; they now keep the `View Job` CTA with a small muted `Closed` label.

## Jobs Listing Fix

- The earlier `No Jobs` state on `/jobs` was caused by demo jobs still having 2025 `expire_date` and `application_closing_date` values while the local environment date is in 2026.
- Category counts are generated from active jobs, so once demo dates were stale the page could feel inconsistent.
- A local migration now refreshes stale published demo jobs into a current open window so the first page of `/jobs` loads visible roles again.

## Account Overview Cleanup

- Replaced the large broken top banner on `/account/overview` with a compact profile header card.
- Fixed the name/location/summary overlap by moving the account header into a dedicated dashboard shell.
- Fixed a real account overview crash by normalizing `jb_saved_jobs` timestamps and ordering saved jobs safely.
- Added overview stat cards for applied jobs, saved jobs, profile completion, and recent applications.
- Added recent applications, saved jobs, recommended jobs, and profile tips panels without introducing heavy new product features.
- Sidebar navigation now includes `My Profile`, `Overview`, `Applied Jobs`, `Saved Jobs`, `CV / Resume`, `Security`, `Experiences`, and `Educations`.

## Employer Flow Cleanup

- Front employer job posting now focuses on title, short summary, company, location, category, job type, salary range, currency, and full description/application instructions.
- Front employer job posting hides technical or stressful fields such as unique IDs, lat/long, job status, approval status, external apply behavior, and extra taxonomy fields.
- The form now explicitly warns that advanced settings remain managed in the background instead of surfacing technical fields in the primary posting flow.
- Front company editing now hides extended admin-style company fields such as tax ID, CEO, office counts, revenue, and other non-essential setup inputs.
- Employer dashboard/profile surfaces no longer show the long internal account unique ID in the sidebar welcome area.
- Public company and job detail pages no longer surface internal unique IDs in the normal viewer experience.

## Page Audit

- `/` remained structurally healthy; navigation and job-card behavior were rechecked after the employer-flow cleanup.
- `/jobs` remained healthy after the earlier active-job fix; this pass focused on card clickability and badge spacing.
- `/jobs/administrative-officer` remained healthy; it now benefits from the cleaner related/listing card treatment and the simplified company/job identity display.
- `/companies` and `/companies/linkedin` remained healthy; the company detail page keeps the cleaner cover behavior from the prior pass.
- `/account/overview` was broken during authenticated probing because of the saved-jobs query bug and is now fixed.
- `/account/dashboard` remained healthy and now avoids showing the long internal account ID in the dashboard shell.
- `/account/jobs/create` remained healthy and now renders the simpler employer posting form with `NGN` and `USD` only.

## Footer / Auth / Newsletter

- Footer content remains JobRango-specific rather than JobBox demo copy.
- Footer navigation remains aligned to job seeker, employer, and company workflows.
- Auth screens remain form-first with no large illustration panel.
- Newsletter keeps the cleaner text-led block without the broken side images.

## Remaining Frontend Artifacts

- Optional blog/candidate/plugin-driven features still exist if their modules remain active.
- Real uploaded company/candidate cover media can still display where present; only the repeated default fallback treatment was reduced.
- The real Botble license reminder is an admin concern and was not removed.
