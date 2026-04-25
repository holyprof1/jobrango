# Job Card Cleanup Report

## Summary

Normal JobRango listing cards were cleaned up so they no longer depend on large image-style presentation. Standard cards now prioritize compact company branding, title, location, job type, salary, description, and apply actions.

## Templates Changed

- `platform/themes/jobbox/partials/job-company-badge.blade.php`
- `platform/themes/jobbox/views/job-board/partials/job-item-grid.blade.php`
- `platform/themes/jobbox/views/job-board/partials/job-item-list.blade.php`
- `platform/themes/jobbox/views/job-board/partials/job-item.blade.php`
- `platform/themes/jobbox/views/job-board/partials/company-job-item.blade.php`
- `platform/themes/jobbox/views/job-board/partials/company-job-items.blade.php`
- `platform/themes/jobbox/views/job-board/partials/job-items.blade.php`
- `platform/themes/jobbox/views/job-board/partials/job-of-the-day-items.blade.php`

## CSS Changed

- `platform/themes/jobbox/assets/sass/_custom.scss`
- `platform/themes/jobbox/public/css/style.css`

## Where The 403 x 257 Placeholder Came From

The theme registers a `featured` media size at `403 x 257` in:

- `platform/themes/jobbox/functions/functions.php`

That size is still valid for intentionally image-led uses, but normal listing cards were updated so they no longer rely on large image presentation patterns.

## New Fallback Behavior

- Normal job cards now render a compact company badge instead of a large visual block.
- If the company has a logo, the logo is shown inside the compact badge.
- If no logo exists, the card uses the theme `default_company_logo` when available.
- If neither a company logo nor a theme default logo exists, the badge shows company initials.

## Company Logo Usage

Yes. Company logos are now the primary visual on normal job cards wherever available.

## Visible Tag Cleanup

- Standard job cards now prefer broad job categories from the job's categories relation.
- If the location text contains `remote`, a `Remote` chip is added.
- This avoids surfacing demo-style tech tags such as `Python`, `Flutter`, `PHP`, `CakePHP`, and `WordPress` on normal listing cards where safer category data exists.

## Pages Checked

Route and template coverage was checked for:

- `/`
- `/jobs`
- `/jobs/project-coordinator`
- `/companies/visa`

Template coverage was also traced for:

- homepage job sections through `job-of-the-day-items`
- jobs listing pages through `job-items`, `job-item-grid`, and `job-item-list`
- company job listings through `company-job-items` and `company-job-item`
- related/simple job listings through `job-item`

## Remaining Places Where Large Job Images Still Appear

Large job images were intentionally left in place where they are appropriate:

- image-led `job-of-the-day` card variants in `platform/themes/jobbox/views/job-board/partials/job-of-the-day-items.blade.php`
- blog post cards and post detail pages
- company profile cover images
- homepage hero and marketing sections

These were not removed because the cleanup was scoped to normal job listing cards only.

## Verification Notes

- Laravel health checked with `php artisan about`
- Route resolution checked for homepage, jobs page, one job detail page, and one company page
- PHP syntax checked for:
  - `platform/themes/jobbox/functions/functions.php`
  - `platform/themes/jobbox/partials/job-company-badge.blade.php`

## Responsive Notes

- Shared card CSS was updated to keep compact logo badges, wrapped taxonomy chips, and 3-line descriptions stable across desktop, tablet, and mobile layouts.
- No browser screenshot runner was available in this session, so responsiveness was verified through template tracing and scoped responsive CSS changes rather than visual browser automation.
