# HAVOC Security Scan Action

Framework-aware adversarial security scanning for web applications. Break your code before attackers do.

## Usage

```yaml
- uses: havoc-security/scan-action@v1
  with:
    api-key: ${{ secrets.HAVOC_API_KEY }}
```

### Full Example

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
          api-key: ${{ secrets.HAVOC_API_KEY }}
          fail-on: high
      - run: echo "Grade: ${{ steps.scan.outputs.grade }}"
```

### Inputs

| Input | Description | Required | Default |
|-------|-------------|----------|---------|
| `api-key` | HAVOC API key | ✅ | — |
| `path` | Path to scan | ❌ | `.` |
| `framework` | Framework (auto-detected) | ❌ | — |
| `fail-on` | Min severity to fail (`critical`, `high`, `medium`, `low`) | ❌ | `high` |
| `cloud-url` | HAVOC Cloud URL | ❌ | `https://havoc.cloud` |

### Outputs

| Output | Description |
|--------|-------------|
| `summary` | Full scan results (JSON) |
| `grade` | Security grade (A-F) |
| `findings-count` | Total findings |
| `critical-count` | Critical findings |

## Get Started

1. Sign up at [havoc.cloud](https://havoc.cloud)
2. Generate an API key in your dashboard
3. Add it as `HAVOC_API_KEY` in your repo secrets
4. Add the action to your workflow

## License

MIT
