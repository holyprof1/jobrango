# Admin License & Update Report

## License Banner

- Source view: `platform/core/base/resources/views/system/license-invalid.blade.php`
- Trigger logic:
  - `platform/core/base/src/Http/Controllers/SystemController.php`
  - `platform/core/setting/src/Http/Controllers/GeneralSettingController.php`
- This is real Botble license enforcement/reminder behavior.
- Proper activation path: `Settings -> General`
- It was intentionally left in place.

## Version Update Notice

- Source controller: `platform/core/base/src/Http/Controllers/SystemController.php`
- Dashboard client component: `platform/core/dashboard/resources/js/components/CheckForUpdates.vue`
- This is an updater notification, not a theme widget.
- Actual update actions still require Botble’s license validation.
- No updater core/license flow was hacked.

## Quick Setup Wizard

- Source package: `platform/packages/get-started`
- Visibility rule: `setting('is_completed_get_started') != '1'`
- Safe action taken: marked complete through a normal setting migration.

## Performance Suggestions

- Shortcode suggestion source: `platform/packages/shortcode/src/Providers/HookServiceProvider.php`
- Widget suggestion source: `platform/packages/widget/src/Providers/HookServiceProvider.php`
- Safe action taken:
  - enabled `shortcode_cache_enabled`
  - enabled `widget_cache_enabled`
  - set both TTL values to `1800`

## Debug Mode Tab

- Source view: `platform/core/base/resources/views/components/debug-badge.blade.php`
- Rendered from: `platform/core/base/resources/views/layouts/master.blade.php`
- It remains because the local environment still has debug mode enabled.
- Production recommendation:
  - `APP_ENV=production`
  - `APP_DEBUG=false`

## What Was Safely Changed

- Quick setup wizard completed through supported settings.
- Shortcode/widget performance prompts handled through supported cache settings.
- Closed/expired jobs removed from public listings through supported job-board settings.

## Admin Sidebar Recommendation

- Keep:
  - Job Board
  - Job Attributes
  - Pages
  - Media
  - Appearance
  - Contact
  - Locations
  - Payments if paid listings/featured jobs are planned
  - Newsletters if email capture remains part of the product
- Hide later from day-to-day workflow:
  - Blog
  - Galleries
  - Teams
  - Testimonials
  - Ads
  - FAQs
  - Plugins
- Risk note:
  - These are plugin-driven modules and should be hidden/disabled only after checking page/widget/theme dependencies.
  - No plugins were deleted in this pass.

## What Was Intentionally Not Bypassed

- Botble license enforcement
- Botble license reminder rendering
- Botble updater license requirements
- Core/vendor license validation code
