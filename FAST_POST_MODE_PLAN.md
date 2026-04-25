# Fast Post Mode Plan

## Goal

Create a future lightweight employer flow that feels closer to a short Google Form than a full admin form.

## Proposed Steps

1. Job title
   - One clear role title
   - Optional short summary helper text

2. Category and job type
   - Broad category selection
   - One main job type with optional secondary type later

3. Location and remote setup
   - City/state or simple location text
   - Remote toggle

4. Salary
   - Currency limited to `NGN` or `USD`
   - Salary from/to
   - Salary range label

5. Application questions
   - Basic mode: default JobRango form
   - Custom mode: add simple text, yes/no, and multiple-choice questions later

6. Publish and share
   - Review screen
   - Publish button
   - Copyable public job link

## Implementation Notes

- Keep the current simplified `/account/jobs/create` form as the base.
- Build the wizard later as a dedicated frontend flow instead of overloading the existing Botble form builder.
- Preserve the current company approval rule so approved companies can publish immediately while non-approved companies still go to moderation.
