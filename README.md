# HAVOC Security Scan Action

Framework-aware adversarial security scanning for **Laravel** applications. Break your code before attackers do.

The action installs [`@havoc-security/cli`](https://www.npmjs.com/package/@havoc-security/cli)
and scans your checkout on the runner. Without `api-key` it makes no network
calls of its own: nothing about your code leaves the runner.

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
          cli-version: '0.5.0'   # pin for reproducible runs
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
| `api-key` | HAVOC Cloud API key. Set it to upload each scan to your dashboard. Leave unset to scan without uploading. | ❌ | — |
| `path` | Path to scan | ❌ | `.` |
| `framework` | Framework to analyse. Laravel is the only implemented value. | ❌ | `laravel` |
| `fail-on` | Minimum severity that fails the build (`critical`, `high`, `medium`, `low`) | ❌ | `high` |
| `cli-version` | Version of `@havoc-security/cli` to install. An exact version, or `latest`/`next`/`beta`. | ❌ | `latest` |
| `cloud-url` | HAVOC Cloud instance to upload to. Only consulted when `api-key` is set. | ❌ | `https://havoc.cloud` |

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

## Uploading to HAVOC Cloud

Set `api-key` and each scan is uploaded to your dashboard, which keeps the
history and the grade over time.

```yaml
      - uses: havoc-security/scan-action@v1
        with:
          api-key: ${{ secrets.HAVOC_API_KEY }}
          cli-version: '0.5.0'
```

Three things worth knowing before you turn it on.

**Create the key as "CI upload only."** The dashboard offers that preset for
exactly this. Such a key can upload a scan result and do nothing else — it
cannot read your findings, your repositories or your team, and it cannot spend
anything. A full-access key pasted into CI can do all of those. Use the
narrow one.

**An upload can never change your build's verdict.** The exit code is decided
by the findings and `fail-on` alone. Every upload failure — no key, a rejected
key, an unreachable host, a spent allowance — is a warning on stderr and
nothing more, so the dashboard being down does not fail your pipeline.

**Uploading needs `@havoc-security/cli` 0.5.0 or later**, the first version
with an upload path. If `cli-version` pins something older the action warns,
scans normally and uploads nothing, rather than passing a flag that version
would reject. Pinning `0.5.0` or leaving `cli-version` at `latest` both work.

What gets uploaded is the complete result for the commit: the findings, the
coverage numbers and the grade, plus the commit SHA, branch and repository
taken from the workflow context. Your source code is not uploaded.

## Getting started

Without a key there is nothing to configure — add the action and push. With one,
create it at [havoc.cloud](https://havoc.cloud), store it as a repository
secret, and pass it as `api-key`.

More about the project: [havoc.cloud](https://havoc.cloud).
Questions: support@havoc.cloud

## Security of this action

Action inputs reach the shell as environment variables, never through `${{ }}`
interpolation into a `run:` body. That distinction matters: the runner
substitutes `${{ }}` textually before bash parses the script, which makes an
input shell source rather than data, and quoting does not contain it. Versions
up to and including **v1.0.2** interpolated `path`, `framework`, `fail-on` and
`cli-version` that way; **v1.0.3** passes every input through `env:`.

If you are pinned to `v1.0.2` or earlier by tag or SHA, move to `v1.0.3`. If you
track `@v1` you already have the fix.

`cli-version` and `cloud-url` are also validated before use, because
`cli-version` reaches `npm install` as a package *spec* — where npm accepts a
git URL or a tarball as readily as a version number.

`.github/workflows/smoke.yml` runs the action against committed fixtures on
every push, including a case asserting that a command substitution passed as
`path` is not executed.

## License

This action is MIT. The CLI and scanner it installs are licensed BUSL-1.1 and
are free to use; see the notices in the published npm packages.
