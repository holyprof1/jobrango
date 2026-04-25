# JobRango Homepage 2 Adjustment Report

Date: 2026-04-25

## Homepage 2 Confirmation

- Active homepage setting: `settings.key = theme-jobbox-homepage_id`, value `2`.
- Active/default homepage page: `Homepage 2`, page ID `2`.
- Page status: `published`.
- Page template: `homepage`, rendered by `platform/themes/jobbox/layouts/homepage.blade.php`.
- Public slug: `homepage-2`, stored in Botble's `slugs` table for `Botble\Page\Models\Page` reference ID `2`.
- Homepage 2 content starts with the `search-box` shortcode using `style="style-2"` and `background_image="pages/banner-section-search-box.png"`.

Other homepage demo pages were not deleted or changed in this task. `Homepage 1`, `Homepage 3`, `Homepage 4`, `Homepage 5`, and `Homepage 6` currently exist as draft pages, with their slugs still present for rollback/history.

## Files Changed

- `platform/themes/jobbox/assets/sass/_custom.scss`
- `platform/themes/jobbox/public/css/style.css`
- `HOMEPAGE_REDESIGN_REPORT.md`

No vendor files, Botble internals, routes, seeders, or database content were changed in this task.

## Hero Background Position Change

Homepage 2's hero background is rendered by:

- Template: `platform/themes/jobbox/partials/shortcodes/search-box.blade.php`
- CSS override: `platform/themes/jobbox/assets/sass/_custom.scss`
- Compiled CSS: `platform/themes/jobbox/public/css/style.css`

Exact CSS adjustment:

```scss
// Before
background-position: right -80px top -36px !important;

// After
background-position: right -80px top -64px !important;
```

Responsive adjustment under `991px`:

```scss
// Before
background-position: center top !important;

// After
background-position: center -18px !important;
```

This moves the existing background image upward slightly without replacing the image, changing the hero structure, changing `background-size`, or zooming the artwork. The intended effect is that the people/illustration elements sit a little higher and read more clearly inside the existing Homepage 2 composition.

A small mobile-only guardrail was also added after screenshot review because the existing hero title could overflow narrow screens:

```scss
@media screen and (max-width: 575px) {
    body {
        overflow-x: hidden;
    }

    .banner-hero.hero-2 {
        width: 100vw;
        max-width: 100vw;
    }

    .banner-hero.hero-2 .hero-2-title {
        max-width: 11ch;
        font-size: clamp(1.85rem, 7.4vw, 2.1rem);
        line-height: 1.12;
        margin-left: auto;
        margin-right: auto;
        overflow-wrap: break-word;
    }

    .banner-hero.hero-2 .banner-inner {
        align-items: center;
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 0 16px;
    }

    .banner-hero.hero-2 .hero-2-panel {
        width: 100%;
        padding: 0 !important;
        text-align: center;
    }

    .banner-hero.hero-2 .hero-2-description {
        max-width: 30ch;
        margin-left: auto;
        margin-right: auto;
    }

    .banner-hero.hero-2 .hero-search-form {
        max-width: 100%;
    }
}
```

## Header And Logo Findings

- No logo size change was made in this task.
- Current custom header CSS already allows a larger logo with `max-height: 72px` on desktop and `64px` under `991px`.
- The active logo is `general/logo.png`, currently a tall image asset at `890x706`, so increasing it more could crowd the nav and auth controls.
- Safest future edit location: `platform/themes/jobbox/assets/sass/_custom.scss`, inside `.header .main-header .header-left .header-logo img`.
- A small mobile header containment rule was added under `991px` after screenshot review so existing desktop min-widths do not create horizontal overflow on phone screens. It hides the desktop auth block on mobile, where the mobile account links already exist in the offcanvas menu.

## Navigation Cleanup Recommendation

No navigation items were removed in this task.

Current main navigation comes from the Botble `main-menu` menu records. The remaining cleanup should be handled later in the admin/database menu records, not by hard-coding the Blade partials.

Recommended later direction:

- Simplify the main nav around `Jobs`, `Companies`, `Post a Job`, and `Sign In`.
- Reduce demo-style dropdowns under `Find a Job`, `Companies`, `Candidates`, and `Pages`.
- Keep the homepage item pointing directly to `Homepage 2`.

## Login Page Findings

- `/login` route is `public.account.login`.
- Template: `platform/themes/jobbox/views/job-board/auth/login.blade.php`.
- Main auth image: `theme-jobbox-auth_background_image_1 = authentication/img-1.png`.
- Secondary auth image: `theme-jobbox-auth_background_image_2 = authentication/img-2.png`.
- `authentication/img-1.png` is a valid wide illustration image at `1505x941`.
- `authentication/img-2.png` is a valid smaller image at `320x200`.

The image/layout issue appears to be CSS/layout related, not a broken image asset. The auth template uses the class `login-register` on both the outer section and the generated auth form (`formClass('login-register text-start mt-20 auth-form')`). Any broad `.login-register` page-level styling can therefore also affect the inner form. There is also absolute-positioned `.img-1` artwork on desktop and hidden artwork on smaller screens, which makes the demo auth layout sensitive to image proportions.

No login page redesign was made in this task. The safest later fix is to scope any auth page wrapper styling to `section.login-register` and then tune only the `.img-1` image placement if needed.

## Risks And Items Left Untouched

- The homepage remains shortcode-driven through Botble CMS.
- The active homepage setting is database-driven and was only inspected, not changed.
- Other homepage demo records remain available as drafts.
- Main menu records were inspected, not changed.
- The login page was inspected and documented, not redesigned.
- Existing local uncommitted public assets/runtime-style changes should be reviewed separately before any broader cleanup commit.
