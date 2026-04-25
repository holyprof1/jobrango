# Admin License & Update Report

## License banner

- Source view: `platform/core/base/resources/views/system/license-invalid.blade.php`
- Trigger flow:
  - `platform/core/base/src/Http/Controllers/SystemController.php`
  - `platform/core/setting/src/Http/Controllers/GeneralSettingController.php`
- This is real Botble license enforcement/reminder behavior.
- Proper activation path: `Settings -> General`
- It was intentionally left in place.

## Version update notice

- Source controller: `platform/core/base/src/Http/Controllers/SystemController.php`
- Dashboard client component: `platform/core/dashboard/resources/js/components/CheckForUpdates.vue`
- This is an updater notification, not a theme-only widget.
- Official update actions remain tied to Botble licensing/activation.
- No updater, version, or license values were faked or bypassed.

## Quick setup wizard

- Source package: `platform/packages/get-started`
- Visibility rule: `setting('is_completed_get_started') != '1'`
- Safe action already applied: marked complete through normal settings data.

## Performance suggestions

- Shortcode suggestion source: `platform/packages/shortcode/src/Providers/HookServiceProvider.php`
- Widget suggestion source: `platform/packages/widget/src/Providers/HookServiceProvider.php`
- Safe action already applied:
  - `shortcode_cache_enabled = 1`
  - `widget_cache_enabled = 1`
  - cache TTL values set for both

## Debug mode tab

- Source view: `platform/core/base/resources/views/components/debug-badge.blade.php`
- Rendered from: `platform/core/base/resources/views/layouts/master.blade.php`
- It remains because the local environment is still running with debug enabled.
- Production recommendation:
  - `APP_ENV=production`
  - `APP_DEBUG=false`

## What was safely changed

- Quick setup wizard completed through supported settings.
- Shortcode/widget performance prompts handled through supported cache settings.
- Local migrations and cache clears were applied safely.

## What was intentionally not bypassed

- Botble license enforcement
- Botble license reminder rendering
- Botble updater license requirements
- Core/vendor license validation code
