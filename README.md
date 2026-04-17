# ePuSta-logfileparser
This is a framework to enrich logfile of a repository with informations. Thes are used by [ePuSta-Server](https://github.com/gbv/ePuSta-Server) to provide Usage Statistics for the repository.

## Requirements:
- php 7.4
- composer

## Installation:

    git clone https://github.com/gbv/ePuSta-logfileparser.git .
    composer prepare
    composer require

## Log File Format (epustalog)

The parser processes web server access log files and enriches each line with additional metadata. The resulting file is called **epustalogfile**.

The framework is designed to support different log file formats. Currently, only an implementation for the Apache Combined Log Format exists.

### Source Format

The source is a web server access log file. Each line represents one HTTP request. Currently supported:

- **Apache Combined Log Format** (the only existing implementation)

Example source line (Apache Combined Log Format):

```
- - - [14/Sep/2025:00:18:47 +0200] "GET /rsc/stat/dfi_mods_00104509.css HTTP/1.1" 200 50 "-" "Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)"
```

### Target Format (epustalogfile)

Each enriched line has the following structure:

```
<uuid> <errors> <sessionId> <documentIdentifier> <associatedIdentifier> <tags> <original log line>
```

All fields are separated by spaces. The structured fields come first, the unmodified copy of the original log line is appended at the end. This ensures reliable parsing even if the original log line contains special characters.

| Field | Description |
|-------|-------------|
| `uuid` | A UUID (v4) uniquely identifying this log entry |
| `errors` | A JSON array of error codes that occurred during processing |
| `sessionId` | A session identifier for the requesting client (or `-` if unknown) |
| `documentIdentifier` | A JSON array of identifiers of the accessed document itself (e.g. MyCoRe ID, DOI, GND) |
| `associatedIdentifier` | A JSON array of identifiers of associated/parent units (e.g. the journal or series the document belongs to) |
| `tags` | A JSON array of tags describing the type of access or properties of the document |
| `original log line` | An unmodified copy of the original source log line |

#### Document Identifier

Contains identifiers of the accessed document itself, e.g.:

- MyCoRe ID
- DOI
- GND

Example: `["dfi_mods_00104509"]`

#### Associated Identifier

Contains identifiers of parent or associated units, e.g. the journal, series, or collection the document belongs to.

Example: `["dfi_mods_00000038"]`

#### Tags

Tags describe the type of access or properties of the document.

Common tag prefixes:

| Prefix | Description |
|--------|-------------|
| `filter:` | Access filter classification (e.g. `filter:robot`) |
| `mir_genre:` | Document genre from MIR/MyCoRe (e.g. `mir_genre:article`) |
| `oas:` | Open Access and content type classification (e.g. `oas:content:robots_abstract`) |

Example: `["mir_genre:article","filter:robot","oas:content:robots_abstract"]`

#### Errors

JSON array of error codes that occurred when processing this line. The errors field is always present, even when no errors occurred. Placing it early in the format ensures error information is preserved even when the original log line could not be parsed.

| Code | Meaning |
|------|---------|
| `E01` | The raw log line could not be parsed (e.g. not valid Apache Combined Log Format) |
| `E02` | The ePuSta log line could not be parsed (malformed epustalogfile line) |

Example (no errors): `[]`

Example (with error): `["E01"]`

### Complete Example

Source line (Apache Combined Log Format):

```
- - - [14/Sep/2025:00:18:47 +0200] "GET /rsc/stat/dfi_mods_00104509.css HTTP/1.1" 200 50 "-" "Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)"
```

Resulting epustalogfile line:

```
11b42b8c-fa92-49ec-9856-d398a731aecf [] - ["dfi_mods_00104509"] ["dfi_mods_00000038"] ["mir_genre:article","filter:robot","oas:content:robots_abstract"] - - - [14/Sep/2025:00:18:47 +0200] "GET /rsc/stat/dfi_mods_00104509.css HTTP/1.1" 200 50 "-" "Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)"
```

The access log is thus enriched with document metadata and access classification, enabling detailed usage statistics per document, session, and access type.

## Scripts

All scripts read configuration from `config/config.ini` and support `--debug` to write diagnostic output to STDERR.

| Script | Description |
|--------|-------------|
| `bin/log2epusta.php` | Converts raw web server log lines (STDIN) to ePuSta format (STDOUT) |
| `bin/filter.php` | Applies access filters (robot detection, HTTP method/status) |
| `bin/anonymize.php` | Anonymizes IP addresses in epustalogfile lines |
| `bin/addIdentifierMIR.php` | Enriches lines with document identifiers from a MIR/MyCoRe repository |
| `bin/addIdentifierOpus4.php` | Enriches lines with document identifiers from an OPUS 4 repository |
| `bin/createSolrImport.php` | Creates Solr import data from enriched log lines |
| `bin/ep_validateEpustaLogfile.php` | Validates that all lines in a file conform to the ePuSta log format |

### ep_validateEpustaLogfile.php

```
Usage: ep_validateEpustaLogfile.php [OPTIONS] <file>

Options:
  -h, --help            Show this help message and exit
  --debug               Show debug output on STDERR
  --only-first-line     Only validate the first line of the file

Exit codes:
  0  All checked lines are valid
  1  One or more lines are invalid
```

## Configuration

Copy `config/config.ini.template` to `config/config.ini` and adjust the values.

| Key | Description | Default |
|-----|-------------|---------|
| `URLLoglineParserClass` | Fully qualified class name of the raw log line parser | `epusta\ApacheLoglineParser` |

The `URLLoglineParserClass` setting allows plugging in a custom parser for log formats other than Apache Combined Log Format. The custom class must extend `epusta\URLLoglineParser`.
