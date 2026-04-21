# ePuSta-logfileparser — Claude Context

## Project Overview

This PHP project parses web server access log files and enriches each line
with document metadata. The resulting files are called **epustalogfiles**.

## Role in the ePuSta ecosystem

Scope of this project: **single-file processing**. Every `bin/` CLI reads
one stream (STDIN / `<file>`) and writes one result (STDOUT). There are no
directory walkers, no cron entry points and no gzip-batch helpers here —
those belong to `ePuSta_tools`.

| Concern | Home project |
|---|---|
| Parse / filter / enrich / anonymize **one** log file | this project |
| Operate on **one** Solr import JSON / one source | `ePuSta-Server` |
| Iterate over directories, batch, cron, gzip handling | `ePuSta_tools` |

If a feature request implies walking a directory or managing many files,
it almost certainly belongs in `ePuSta_tools`, not here.

## Key Concepts

- **Source**: Web server access log files (currently: Apache Combined Log Format)
- **Output**: epustalogfiles — each line is the original log line prefixed with a UUID, errors, and metadata fields
- **Purpose**: Enables usage statistics for document repositories (MyCoRe/MIR-based systems)

## File Format

Each epustalogfile line has this structure:

```
<uuid> <errors> <sessionId> <documentIdentifier> <associatedIdentifier> <tags> <original log line>
```

| Field | Description |
|-------|-------------|
| `uuid` | UUID v4 identifying this log entry |
| `errors` | JSON array of error codes (e.g. `[]`, `["E01"]`) |
| `sessionId` | Session identifier or `-` if unknown |
| `documentIdentifier` | JSON array of document identifiers |
| `associatedIdentifier` | JSON array of associated/parent identifiers |
| `tags` | JSON array of access classification tags |
| `original log line` | Unmodified copy of the source log line |

See `README.md` for the full specification.

## Error Codes

Defined in `php/src/ePuStaErrors.php`:

| Code | Meaning |
|------|---------|
| `E01` | Raw log line could not be parsed (e.g. invalid Apache format) |
| `E02` | ePuSta log line could not be parsed (malformed epustalogfile line) |

## Important Classes

### Core

- `ePuStaLogline` (`php/src/ePuStaLogline.php`) — represents an epustalogfile line; has `uuid`, `errors`, `sessionId`, `documentIdentifier`, `associatedIdentifier`, `tags`, `rawLogline`, `urlLogline`; `__toString()` serializes to the epustalogfile format; `convertLogline($line)` converts a raw log line to a minimal epustalogfile line
- `ePuStaLoglineParser` (`php/src/ePuStaLoglineParser.php`) — parses epustalogfile lines; configurable via constructor `__construct(string $urlLoglineParserClass = ApacheLoglineParser::class, bool $debug = false)`; merges URL parser errors into `$logline->errors`; writes to STDERR only when `$debug = true`
- `ePuStaErrors` (`php/src/ePuStaErrors.php`) — defines error code constants (`E01`, `E02`)

### URL Log Line Parsers

- `URLLoglineParser` (`php/src/URLLoglineParser.php`) — abstract base class; defines `parse(string $rawLogline, URLLogline &$logline, array &$errors): bool`
- `ApacheLoglineParser` (`php/src/ApacheLoglineParser.php`) — extends `URLLoglineParser`; parses Apache Combined Log Format; sets `E01` in `$errors` on failure
- `URLLogline` (`php/src/URLLogline.php`) — represents a parsed URL log line (extended by `ApacheLogline`)
- `ApacheLogline` (`php/src/ApacheLogline.php`) — represents a parsed Apache log line

### Filters and Enrichment

- `FilterRobots.php`, `FilterHttpMethod.php`, `FilterHttpStatus.php` — classify access types
- `Counter3Filter30sek.php` — COUNTER 3 compliant filtering (30-second session deduplication)

### Repository-specific

- `php/src/mir/` — MIR (MyCoRe) repository-specific identifier enrichment
- `php/src/Opus4/` — OPUS 4 repository-specific identifier enrichment

## Bin Scripts

All scripts:
- Read configuration from `config/config.ini` (via `epusta\Configuration`)
- Support `-h`/`--help` for usage information
- Support `--debug` to enable STDERR diagnostic output
- Read `URLLoglineParserClass` from config and pass to `ePuStaLoglineParser`

| Script | Description |
|--------|-------------|
| `bin/log2epusta.php` | Converts raw log lines (STDIN) to ePuSta format (STDOUT) |
| `bin/filter.php` | Applies access filters |
| `bin/anonymize.php` | Anonymizes IP addresses |
| `bin/addIdentifierMIR.php` | Enriches with MIR/MyCoRe document identifiers |
| `bin/addIdentifierOpus4.php` | Enriches with OPUS 4 document identifiers |
| `bin/createSolrImport.php` | Creates Solr import data |
| `bin/ep_validateEpustaLogfile.php` | Validates a file against the ePuSta log format; also supports `--only-first-line` |

## Configuration

`config/config.ini.template` → copy to `config/config.ini`

Key settings:

| Key | Description | Default |
|-----|-------------|---------|
| `URLLoglineParserClass` | Fully qualified class name of the URL log line parser | `epusta\ApacheLoglineParser` |

## Git Workflow

Commits, pull requests, and branches are prefixed with the Jira ticket ID.

Examples:
- Branch: `REP-1080-new-logline-format`
- Commit: `REP-1080 add new logline format parser`
- PR title: `REP-1080 add new logline format parser`

## Coding Notes

- Namespace: `epusta`
- PHP 7.4+ compatibility required
- Code style enforced via `phpcs.xml`
- Tests via PHPUnit (`phpunit.xml`)
- PSR-4 autoloading: `epusta\` → `php/src/`
