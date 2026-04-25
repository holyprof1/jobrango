# Security Audit Report

## Scope

This report focuses on the current JobRango issues that were checked during this task:

- admin login failure
- admin license/warning screens
- secrets and tracked files
- SQL dump exposure
- default credentials
- installer exposure
- production readiness items

## Findings

### 1. Admin Login Database Error

Root cause:

- Botble ACL expects a nullable `users.sessions_invalidated_at` column.
- The current codebase writes to that column during login and password/session invalidation handling.
- The local database was missing that column, which caused the admin login SQL error.

Relevant code references:

- `platform/core/acl/src/Listeners/LoginListener.php`
- `platform/core/acl/src/Http/Middleware/Authenticate.php`
- `platform/core/acl/src/Services/ChangePasswordService.php`
- `platform/core/acl/src/Models/User.php`

Safe fix used:

- applied the official Botble ACL migration:
  `platform/core/acl/database/migrations/2025_11_30_100000_add_sessions_invalidated_at_to_users_table.php`

Result:

- admin login now works
- admin dashboard loads
- no 500 error during the tested login flow

## Admin Notices Audit

### License notice

Source:

- `platform/core/base/resources/views/system/license-invalid.blade.php`
- injected from `platform/core/base/resources/views/layouts/master.blade.php`
- backed by Botble license logic in `platform/core/base/src/Supports/Core.php`

What it is:

- a real Botble commercial license activation reminder
- not a fake demo banner
- not something that should be bypassed or removed in code

Current state on this local install:

- license-related settings are empty
- the admin dashboard shows the invalid license warning banner and activation modal

Does it block functionality?

- it does not block admin login or dashboard access in the current local test
- it behaves like a persistent admin notice/reminder
- production use still requires a valid license per vendor terms

Legitimate options:

- enter a valid license/Envato purchase code through the admin license/settings flow
- leave it as-is in local development if no production activation is needed yet
- do not modify vendor/core licensing code to suppress it

### Quick setup / weak password warning

Source:

- `platform/packages/get-started/resources/views/index.blade.php`
- related language strings in `platform/packages/get-started/resources/lang/en/get-started.php`

What it is:

- a demo/onboarding wizard
- includes a warning that the default admin account uses a weak password
- separate from the license notice

Action taken:

- the follow-up JobRango cleanup migration now marks the setup wizard complete through the supported `is_completed_get_started` setting
- the local admin password is also rotated to a stronger local-demo credential

## Secrets and Tracked Files

### `.env`

Check result:

- `.env` exists locally
- `git ls-files .env` returned no tracked file

Recommendation:

- keep `.env` untracked
- rotate any real secrets before production if this install used shared/demo credentials

## Database Dump Exposure

### `database.sql`

Check result:

- `database.sql` exists at the project root
- requesting `http://127.0.0.1:8000/database.sql` returned `404`

Why:

- Laravel serves the app from the `public` web root
- the SQL dump is outside that public web root in this setup

Recommendation:

- keep SQL dumps outside `public`
- do not deploy local dump files unnecessarily

## Backups / Archives / Install Files

### Backup/archive scan

Scan result outside vendor/node_modules:

- `database.sql`

No extra zip/rar/bak dump pile was found in the project root scan.

### Installer exposure

Route list still contains installer routes, but requesting `/install/welcome` redirected to `/` on this local install.

Recommendation:

- keep installer inaccessible in production
- confirm final production deploy does not expose a usable installer path

## Default Credentials

Current local admin login documented for this task:

- username: `admin`
- email: `admin@jobrango.test`
- password: `JobRango123!`

This is acceptable for local/demo use only.

Required action before production:

- rotate or replace all demo credentials
- review any other seeded/demo users before launch

## Debug Mode

Current local environment:

- `APP_DEBUG=1`

Recommendation:

- `APP_DEBUG` must be `false` in production
- production should also use proper error logging instead of public stack traces

## Admin Path

Current admin path behavior:

- admin is reachable at `/admin/login`
- config default comes from `platform/core/base/config/general.php`
- default fallback is `ADMIN_DIR=admin`

Recommendation:

- change `ADMIN_DIR` before launch to reduce automated targeting of the default admin URL

## Upload / Media Notes

Media is served through the normal Botble/Laravel public storage/media flow.

Practical production notes:

- keep upload MIME rules and file size limits reviewed
- avoid allowing dangerous executable file types
- use least-privilege permissions on writable upload directories
- monitor public uploads and media manager access by role

## Production Readiness Summary

Before production, JobRango should:

- activate a valid Botble license
- remove or rotate the documented local demo admin password
- change the default admin path from `/admin`
- set `APP_DEBUG=false`
- verify installer is not usable
- review seeded/demo users and content
- review upload/media restrictions and admin permissions
