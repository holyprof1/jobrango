# Application Form Strategy

## Current Release

The current release intentionally ships the application-form workflow in two layers:

### Implemented Now

- After an employer saves a job, they are sent to `Customize Application Form`.
- Each job now stores:
  - `application_mode`
  - `application_form_schema`
  - `application_form_settings`
- Employers can choose:
  - `Basic application`
  - `Custom application`
- Employers can also save early screening preferences:
  - auto-highlight applicants later
  - mark incomplete applications when required questions are missing

### Honest Limitation

The full question builder is not live in this release.

- Candidate applications still use the standard JobRango apply flow.
- The custom mode currently acts as a reserved configuration state plus UX placeholder.
- No fake drag/drop builder or pretend custom form runtime was added.

## Planned Builder Shape

The data model is prepared for a future builder with question types such as:

- Short answer
- Paragraph
- Multiple choice
- Checkbox
- Phone
- Email
- File upload
- CV upload
- Yes/No

Planned next-step capabilities:

- author questions in employer UI
- save required/optional per question
- render job-specific apply forms to candidates
- add knockout/scoring rules later

## Why This Approach

- It keeps the employer flow calm now.
- It avoids pretending a full builder works when it does not.
- It preserves a clean migration path for the next phase without redoing the job model again.
