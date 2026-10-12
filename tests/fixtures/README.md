# Smoke-test fixtures

Two throwaway Laravel-shaped trees used by `.github/workflows/smoke.yml`. They
are not an application and are not scanned by anything else.

- `vulnerable/` — an unguarded admin route. Produces a **high** finding, so the
  action must exit non-zero with the default `fail-on: high`.
- `clean/` — no routes and no controller actions. Produces only findings below
  the threshold, so the action must exit **0**.

Between them they cover the behaviour that the published v1 got wrong: an exit
code that reflects the scan, and outputs that are actually populated.
