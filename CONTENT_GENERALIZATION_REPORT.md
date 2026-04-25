# JobRango Homepage And Content Cleanup Report

Date: 2026-04-25

## Homepage 2 Status

- Active homepage setting: `settings.key = theme-jobbox-homepage_id`, value `2`.
- Active/default homepage page: `Homepage 2`, page ID `2`.
- Homepage 2 renders through `platform/themes/jobbox/layouts/homepage.blade.php`.
- Its hero is the `search-box` shortcode with `style="style-2"`.

## Hero Eyebrow Removal

- The visible eyebrow text came from the Homepage 2 `search-box` shortcode attribute `subtitle="Find your range of work."`.
- That subtitle was rendered only by the style-2 branch in `platform/themes/jobbox/partials/shortcodes/search-box.blade.php`.
- The style-2 eyebrow block was removed from the template, so this hero no longer prints `<span class="hero-2-eyebrow">...`.
- The active local DB page content, `database/seeders/PageSeeder.php`, and `database.sql` were also updated so Homepage 2 no longer carries the eyebrow subtitle source.
- The main headline, search form, counters, and Homepage 2 structure were left intact.

## Mobile Header

Before:

- `platform/themes/jobbox/partials/navbar.blade.php` already contained both the desktop header and a mobile offcanvas menu.
- The custom homepage CSS kept `.nav-main-menu` displayed, so the desktop navigation could remain visible at mobile widths.

After:

- `platform/themes/jobbox/assets/sass/_custom.scss` now hides `.nav-main-menu` and `.main-menu` at `max-width: 991px`.
- The existing `.burger-icon` is kept visible and aligned so the existing mobile offcanvas menu remains accessible.
- `.header-right` was already hidden in the same mobile block; login/register remain available inside `.mobile-account` in the mobile menu.
- No new mobile navigation system was invented.

Exact Sass change:

```scss
@media screen and (max-width: 991px) {
    .header {
        .nav-main-menu,
        .main-menu {
            display: none !important;
        }

        .burger-icon {
            display: inline-block;
            position: relative;
            top: auto;
            right: auto;
        }

        .main-header {
            .header-nav {
                flex: 0 0 auto;
                justify-content: flex-end;
                width: auto;
            }
        }
    }
}
```

## Content Generalization

Updated visible/demo content away from IT-only positioning:

- Homepage 2 popular/trending searches now use `Administration`, `Sales & Marketing`, `Customer Service`, and `Remote Work`.
- Homepage 2 jobs section now references administration, sales, service, operations, finance, healthcare, education, hospitality, and skilled trade openings.
- Job categories now use broad categories: Administration, Sales & Marketing, Customer Service, Operations, Logistics & Delivery, Finance & Accounting, Healthcare, Education & Training, Hospitality, and Construction & Skilled Trade.
- Demo job titles now include broad roles such as Administrative Officer, Sales Representative, Customer Service Representative, Logistics Coordinator, Delivery Rider, Finance Officer, Healthcare Assistant, Teacher, Hotel Front Desk Officer, Electrician, Security Officer, Cleaner and Facility Support, Graduate Trainee, Remote Customer Support Associate, and Contract Field Enumerator.
- Demo job tags now use Administration, Sales, Customer Service, Logistics, Finance, Healthcare, Education, and Field Work.
- The `/jobs` page hero copy was updated from PHP/developer-specific wording to broad sector job search wording.

Files/sources updated for content:

- `database/seeders/PageSeeder.php`
- `database/seeders/JobCategorySeeder.php`
- `database/seeders/JobSeeder.php`
- `database.sql`
- Active local DB pages/categories/tags/jobs were updated to match the source changes.

## IT-Only Content Still Left

Some inactive or non-homepage demo content still contains old demo terms and was intentionally left for a later content pass:

- Inactive demo homepages still include some creative/design/development language.
- Blog demo taxonomy still includes a `Design` category.
- Company/demo slugs still include third-party demo names such as `adobe-illustrator`.
- Some candidate/team/testimonial/sample taxonomy seeders may still contain generic tech-demo wording.

These were left untouched because this task was scoped to Homepage 2, mobile header behavior, `/jobs`, visible homepage demo defaults, and the core job/category demo data.

## Navigation Recommendation

For a later navigation cleanup, simplify the menu around:

- Jobs
- Companies
- Post a Job / Get Started
- Sign In

Then reduce the Pages/demo dropdowns once the client approves which demo pages should remain available.

## Login Page Inspection

- `/login` is controlled by `platform/themes/jobbox/views/job-board/auth/login.blade.php`.
- The image paths come from theme options:
  - `theme-jobbox-auth_background_image_1 = authentication/img-1.png`
  - `theme-jobbox-auth_background_image_2 = authentication/img-2.png`
- The base layout CSS is in `platform/themes/jobbox/assets/sass/pages/_login-register.scss`.
- The layout issue is mainly a theme/demo layout issue: small decorative auth PNGs are absolutely positioned around a narrow form layout, which can look broken or awkward when the page is branded for JobRango.
- There are pre-existing uncommitted auth CSS edits in `_custom.scss` and compiled CSS that appear to be an attempted login layout improvement. Those edits were not included in this task commit.
- No login redesign was done in this task.

## Risks And Items Left Untouched

- No vendor files or Botble internals were changed.
- Homepage 2 structure and sections were preserved.
- Header/logo size was not changed in this task.
- Existing unrelated local changes were intentionally left uncommitted.
