# Front-end Testing Plan

This document outlines the strategy for testing the front-end of the LevelUp application.

## Unit testing

* **Scope:** Components, composables and utilities located under `resources/js/`.
* **Framework:** Custom Vitest-compatible harness (`tools/vitest`) executed through `npm run test`.
* **Approach:**
  * Render Vue single-file components in isolation using a Vite-powered server-side renderer with lightweight stubs for framework dependencies (Inertia, layout shells, UI atoms).
  * Assert form behaviours (submission, validation feedback) by inspecting component state returned from `setup()` and simulated Inertia form responses.
  * Validate accessibility helpers using an `axe-core` inspired analyser tailored to key navigation landmarks.
* **Automation:** All unit tests reside in `resources/js/__tests__/*.spec.mjs` and run in CI via `npm run test`.

## Functional testing

* **Focus areas:** Critical user paths such as authentication, gameplay interactions (tips, comments, ratings) and navigation.
* **Method:**
  * Compose higher-level component tests that exercise multiple child components together (e.g., `resources/js/pages/games/Show.vue`).
  * Stub backend calls via the Inertia form abstraction to avoid reliance on live APIs while preserving submission logic and callbacks.
  * Extend coverage incrementally for new UI flows, ensuring every regression bug gains a dedicated spec.
* **Data management:** Provide deterministic props/fixtures for each component to guarantee reproducible renders and state transitions.

## Browser compatibility

* **Target browsers:** Latest stable releases of Chrome, Firefox, Edge and Safari.
* **Verification workflow:**
  * During development, smoke-test responsive layouts in local browsers using Vite's dev server.
  * For release readiness, execute a manual checklist covering authentication, navigation and gameplay pages across the target browsers at common breakpoints (mobile, tablet, desktop).
  * Track issues and follow-up work in the project backlog; add automated coverage when feasible (e.g., Playwright scenarios) to minimise manual re-testing.

## Continuous integration

* `npm run test` executes the unit suite inside GitHub Actions (`.github/workflows/tests.yml`).
* JavaScript tests run alongside existing PHP checks so regressions surface early in the PR lifecycle.
* Failures block merges until resolved, reinforcing a “green main branch” policy.

## Maintenance

* Document new specs with a short comment explaining intent and critical paths exercised.
* Favour descriptive test names that read like documentation.
* Regularly prune obsolete stubs or fixtures when components evolve to reduce noise in the suite.
