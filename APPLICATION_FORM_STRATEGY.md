# Application Form Strategy

## What exists now

- JobRango already supports the default Botble job application flow.
- Applicants can apply from the public job detail page.
- Employers can post jobs without paid-credit blockers while free mode is enabled.
- External apply URLs exist in the underlying platform, but the simplified employer frontend form now hides that option to reduce friction.

## Recommended product modes

### Basic mode

- Keep the current default application form as the standard path.
- Best for most employers during the free-launch phase.
- Minimal candidate friction and no extra configuration burden.

### Custom mode

- Future enhancement for employers who want their own screening questions.
- Suggested field types:
  - short text
  - paragraph
  - yes/no
  - single choice
  - multiple choice
  - file upload

## Public application links

- Each published job already has a public detail URL that can act as the shareable application entry point.
- A future custom application mode can still reuse that public URL and render employer-specific questions beneath the main job content.

## What still needs building later

- Employer-managed custom question builder
- Stored per-job application schema
- Applicant response rendering in the employer dashboard
- Validation/reporting for custom answers
- Optional reusable question templates for repeat hiring
