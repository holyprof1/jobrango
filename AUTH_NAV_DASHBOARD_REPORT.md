# Auth Nav and Dashboard Report

## Navigation behavior

- Guest header now shows the core public navigation (`Home`, `Jobs`, `Companies`, `For Employers`) plus the guest auth actions (`Sign In`, `Get Started`).
- Logged-in job seekers no longer see `Sign In` in the desktop or mobile header. They now get a direct `Dashboard` link plus the existing account dropdown with `Saved Jobs`, `Applied Jobs`, `Account Settings`, and `Logout`.
- Logged-in employers no longer see `Sign In` in the desktop or mobile header. They now get direct `Employer Dashboard` and `Post a Job` links plus the existing account dropdown with `My Companies`, `Account Settings`, and `Logout`.
- The theme still uses the Botble account dropdown pattern rather than replacing account handling with a custom auth component.
- The job seeker account shell was rebuilt into a compact dashboard layout with a profile header, sidebar navigation, and overview stat cards so `/account/overview` no longer has the oversized broken profile banner.

## Redirect routes

- Job seeker login redirect: `/account/overview`
- Job seeker registration redirect: `/account/overview`
- Employer login redirect: `/account/dashboard`
- Employer registration redirect: `/account/dashboard`
- Logged-in job seeker hitting `/login` or `/register`: redirected to `/account/overview`
- Logged-in employer hitting `/login` or `/register`: redirected to `/account/dashboard`

## Related account routes

- Shared employer dashboard route: `/account/dashboard`
- Job seeker account overview route: `/account/overview`
- Account settings route: `/account/settings`
- Employer company management route: `/account/companies`
- Employer post-job route: `/account/jobs/create`
- Logout route: `/account/logout`

## Files changed

- `platform/plugins/job-board/src/Http/Controllers/Fronts/AccountController.php`
- `platform/plugins/job-board/src/Http/Controllers/Auth/LoginController.php`
- `platform/plugins/job-board/src/Http/Controllers/Auth/RegisterController.php`
- `platform/plugins/job-board/src/Http/Middleware/RedirectIfAccount.php`
- `platform/themes/jobbox/partials/main-menu.blade.php`
- `platform/themes/jobbox/partials/navbar.blade.php`
- `platform/themes/jobbox/views/job-board/account/overview.blade.php`
- `platform/themes/jobbox/views/job-board/account/partials/layout-settings.blade.php`
- `database/migrations/2026_04_25_223000_clean_authenticated_flows_and_free_mode.php`

## Remaining limitations

- Browser-level login form submission remains awkward to verify from the CLI because of local session/CSRF handling in the HTTP client path.
- Admin authentication is unchanged; this cleanup focused on the frontend account guard and account dashboard routing.
