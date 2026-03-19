# ePuSta-logfileparser — Claude Context

## Project Overview

This PHP project parses Apache Combined Log Format access log files and enriches each line with document metadata. The resulting files are called **epustalogfiles**.

## Key Concepts

- **Source**: Apache access log files (Combined Log Format)
- **Output**: epustalogfiles — each line is the original log line prefixed with a UUID and appended with three fields: SessionID, identifier JSON array, subjects JSON array
- **Purpose**: Enables usage statistics for document repositories (MyCoRe/MIR-based systems)

## File Format

See `README.md` for the full epustalogfile format specification.

## Important Classes

- `ApacheLogline` (`php/src/ApacheLogline.php`) — represents a parsed Apache log line
- `ConvertedLogline` (`php/src/ConvertedLogline.php`) — extends `ApacheLogline` with `uuid`, `sessionId`, `identifier`, and `subjects` fields; serializes to the epustalog line format
- `ConvertedLoglineParser` (`php/src/ConvertedLoglineParser.php`) — parses epustalogfile lines back into `ConvertedLogline` objects
- `ApacheLoglineParser.php` — parses raw Apache log lines
- `FilterRobots.php`, `FilterHttpMethod.php`, `FilterHttpStatus.php` — filter classes for classifying access types
- `Counter3Filter30sek.php` — COUNTER 3 compliant filtering (30-second session deduplication)

## Repository-specific Parsers

- `php/src/mir/` — MIR (MyCoRe) repository-specific enrichment
- `php/src/Opus4/` — OPUS 4 repository-specific enrichment

## Coding Notes

- Namespace: `epusta`
- PHP 7.4 compatibility required
- Code style enforced via `phpcs.xml`
- Tests via PHPUnit (`phpunit.xml`)
