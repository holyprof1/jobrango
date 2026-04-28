# Test Accounts

These credentials are for local/demo testing only. Change or remove them before production.

Validation status on 2026-04-28:

- Job seeker credentials: valid
- Employer credentials: valid
- Admin credentials: valid

## Admin

- URL: `/admin/login`
- Username: `admin`
- Email: `admin@jobrango.test`
- Password: `JobRango123!`

## Job Seeker / Applicant

- URL: `/login`
- Email: `jobseeker@jobrango.test`
- Password: `JobRango123!`
- Dashboard route: `/account/overview`
- Resume/CV management: `/account/settings`
- Account type: `job seeker`

## Employer / Talent Finder

- URL: `/login`
- Email: `employer@jobrango.test`
- Password: `JobRango123!`
- Dashboard route: `/account/dashboard`
- Company: `JobRango Talent Partners`
- Account type: `employer`
- Job posting route: `/account/jobs/create`
- Application form setup route pattern: `/account/jobs/{job}/application-form`
- Applicants by job route pattern: `/account/applicants?job_id={job}`

## Smoke Notes

- Employer route smoke test resolved a sample job route:
  - `JR-JOB-52`
- Employer company edit route resolved:
  - `/account/companies/edit/21`
- Public company route resolved:
  - `/companies/honda`
- Admin company management route resolved:
  - `/admin/job-board/companies`
