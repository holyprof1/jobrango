# Fast Post Mode Plan

## Goal

- Add a lighter employer posting experience before building a full multi-step wizard.
- Keep advanced fields available lower on the page or behind an advanced section, but lead with the essentials.

## Suggested step order

1. Title
2. Category / Job Type
3. Location / Remote
4. Salary / Currency
5. Application Questions
6. Publish / Share Link

## Practical rollout

- Current state:
  - the frontend employer job form has already been simplified to foreground the most important posting fields
  - currency is now restricted to `NGN` and `USD` in that front form
- Next step:
  - convert the simplified form into a real staged wizard only when the current publish flow is fully stable

## Advanced settings handling

- Keep advanced settings lower on the page or in a collapsible section.
- Technical items such as unique IDs, moderation state, hidden-company toggles, and low-level behavior flags should stay out of the primary fast-post path.
