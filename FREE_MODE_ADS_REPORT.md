# Free Mode & Ads Report

## Free mode status

- The site is now running in free mode through the supported setting `job_board_enable_credits_system = 0`.
- Employers can post jobs without being blocked by buy-credit/package prompts.
- The account dashboard no longer renders the `Buy Credits` summary block, package warning alert, or package menu links when the credits system is off.
- The employer posting form now defaults to `NGN` and only exposes `NGN` or `USD` as visible salary-currency choices in the frontend dashboard flow.
- The frontend dashboard currency switcher/footer now only shows `NGN` and `USD`, keeping demo currencies like `EUR` out of the employer journey.
- Frontend job posting now follows company approval rules:
  - published/approved companies auto-approve their jobs
  - non-approved companies still require admin approval

## What was hidden from the user journey

- Frontend account/package prompts driven by `JobBoardHelper::isEnabledCreditsSystem()`
- Credit summary panel in `platform/plugins/job-board/resources/views/themes/dashboard/layouts/menu-top.blade.php`
- Credit warning alert in `platform/plugins/job-board/resources/views/themes/dashboard/layouts/body.blade.php`
- Package/account menu items registered conditionally in `platform/plugins/job-board/src/Providers/JobBoardServiceProvider.php`
- Frontend job-form fields that felt admin-only or overly technical, including unique IDs, lat/long, approval status, external apply behavior, and extra salary/privacy toggles

## What remains available in admin

- Payment plugin/module remains installed.
- Payment settings remain available in admin for future monetization.
- Job Board package/credits code remains in place and can be re-enabled later by turning the credits system setting back on.
- Admin currency settings remain available, so broader currency support can be restored later if needed.

## Ads findings

- The Ads module remains installed and admin-manageable.
- Current local database inspection did not show active ad rows, so there were no visible demo ads to hide in this pass.
- Ads placements are still available in theme locations such as:
  - `main_content_before` / `main_content_after`
  - `job_list_before` / `job_list_after`
  - `job_before` / `job_after`
  - `company_before` / `company_after`
  - `company_sidebar_before` / `company_sidebar_after`
  - footer/blog/post candidate locations

## Recommended future ad placement

- Homepage: one clean placement after the hero or after the first featured jobs block.
- Jobs listing: one sidebar or between-results placement, not both stacked aggressively.
- Company page: one lower-page placement after company details or related jobs.
- Blog pages: only if blog becomes an active content channel later.

## How to re-enable monetization later

- Turn `job_board_enable_credits_system` back on from the Job Board settings flow or a controlled migration.
- Configure packages/pricing in the Job Board package area.
- Keep payment gateways configured in admin before exposing package purchase links again.
