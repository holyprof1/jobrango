# Frontend Cleanup Report

## Summary

- Job cards were tightened so homepage/listing cards no longer render job descriptions.
- Card metadata is now limited to the essential items: company, title, location, job type, posted date, salary, and CTA.
- Visible card tags were reduced to a maximum of 2.
- The large demo-style image treatment remains removed from auth/newsletter surfaces.

## Job Card Changes

- Removed description/excerpt output from grid/list/company/homepage job-card partials.
- Reduced visible category chips from 3 to 2 to keep cards shorter.
- Improved badge spacing and wrapping in `platform/themes/jobbox/assets/sass/_custom.scss` so the job-type badge no longer overlaps the company/title area.
- Kept the company logo/initial badge partial, so the big placeholder image does not return.

## Closed Jobs

- Public closed-job listing is now disabled through supported settings.
- Public expired-job listing is also disabled through supported settings.
- This keeps featured/homepage cards focused on active roles instead of showing “Closed” as the main action.

## Footer / Auth / Newsletter

- Footer content remains JobRango-specific rather than JobBox demo copy.
- Footer navigation was aligned to job seeker/employer/company workflows via database/menu updates.
- Auth screens remain form-first with no large illustration panel.
- Newsletter keeps the cleaner text-led block without the broken side images.
- The big newsletter/demo image placeholders remain removed.

## Remaining Frontend Artifacts

- Optional blog/candidate/plugin-driven features still exist in the product if their modules remain active.
- The real Botble license reminder is an admin concern and was not removed.
