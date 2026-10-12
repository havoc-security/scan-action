# HAVOC Security Scan Action

Framework-aware adversarial security scanning for **Laravel** applications. Break your code before attackers do.

The action installs [`@havoc-security/cli`](https://www.npmjs.com/package/@havoc-security/cli)
and scans your checkout on the runner. It needs no account, no API key and no
network calls of its own: nothing about your code leaves the runner.

## Usage

```yaml
- uses: havoc-security/scan-action@v1
```

### Full example

```yaml
name: Security Scan
on: [push, pull_request]

jobs:
  havoc:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: havoc-security/scan-action@v1
        id: scan
        with:
          fail-on: high
          cli-version: '0.4.1'   # pin for reproducible runs
      - run: echo "Grade ${{ steps.scan.outputs.grade }} — ${{ steps.scan.outputs.findings-count }} findings"
        if: always()
```

Keeping the full result:

```yaml
      - uses: havoc-security/scan-action@v1
        id: scan
      - uses: actions/upload-artifact@v4
        if: always()
        with:
          name: havoc-results
          path: ${{ steps.scan.outputs.results-path }}
```

### Inputs

| Input | Description | Required | Default |
|-------|-------------|----------|---------|
| `api-key` | **Reserved — not yet implemented.** Results are not uploaded anywhere. Leave unset. | ❌ | — |
| `path` | Path to scan | ❌ | `.` |
| `framework` | Framework to analyse. Laravel is the only implemented value. | ❌ | `laravel` |
| `fail-on` | Minimum severity that fails the build (`critical`, `high`, `medium`, `low`) | ❌ | `high` |
| `cli-version` | Version of `@havoc-security/cli` to install | ❌ | `latest` |
| `cloud-url` | **Reserved — not yet implemented.** Has no effect. | ❌ | `https://havoc.cloud` |

### Outputs

| Output | Description |
|--------|-------------|
| `grade` | Security grade (A–F) |
| `findings-count` | Total findings |
| `critical-count` | Critical findings |
| `high-count` | High-severity findings |
| `authz-coverage-percent` | Controller actions with a detectable authorization check |
| `route-auth-percent` | Route-level **authentication** percentage — a different measure from the above |
| `summary` | Full scan result as compact JSON. On very large results the findings array is omitted; read `results-path` instead. |
| `results-path` | Path to the complete JSON result on the runner |

The job summary is written to the workflow run regardless of outcome.

### Failing the build

`fail-on` is enforced by the CLI's exit code, which this action preserves. With
the default `high`, a single high or critical finding fails the step. Set
`continue-on-error: true` on the step if you want the scan to report without
blocking.

## Getting started

Nothing to configure — add the action and push. There is no account to create
and no key to store.

The `api-key` and `cloud-url` inputs are accepted but **do nothing today**: no
published version of the CLI reads them and the scan makes no network calls.
They are declared so that workflows written against earlier versions, which
documented `api-key` as required, keep running without a warning. Uploading
results to a dashboard is not implemented; this README will say so when it is.

More about the project: [havoc.cloud](https://havoc.cloud).
Questions: support@havoc.cloud

## License

This action is MIT. The CLI and scanner it installs are licensed BUSL-1.1 and
are free to use; see the notices in the published npm packages.
