# ePuSta
Access Statistics about the usage of electronic Publications

This is a framework to provide Usage Statistics for repositories.

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
<uuid> <sessionId> <documentIdentifier> <associatedIdentifier> <tags> <errors> <original log line>
```

All fields are separated by spaces. The structured fields come first, the unmodified copy of the original log line is appended at the end. This ensures reliable parsing even if the original log line contains special characters.

| Field | Description |
|-------|-------------|
| `uuid` | A UUID (v4) uniquely identifying this log entry |
| `sessionId` | A session identifier for the requesting client (or `-` if unknown) |
| `documentIdentifier` | A JSON array of identifiers of the accessed document itself (e.g. MyCoRe ID, DOI, GND) |
| `associatedIdentifier` | A JSON array of identifiers of associated/parent units (e.g. the journal or series the document belongs to) |
| `tags` | A JSON array of tags describing the type of access or properties of the document |
| `errors` | A JSON array of error codes that occurred during enrichment |
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

JSON array of error codes that occurred when enriching this line. Empty if no errors occurred.

Example: `[]`

### Complete Example

Source line (Apache Combined Log Format):

```
- - - [14/Sep/2025:00:18:47 +0200] "GET /rsc/stat/dfi_mods_00104509.css HTTP/1.1" 200 50 "-" "Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)"
```

Resulting epustalogfile line:

```
11b42b8c-fa92-49ec-9856-d398a731aecf - ["dfi_mods_00104509"] ["dfi_mods_00000038"] ["mir_genre:article","filter:robot","oas:content:robots_abstract"] [] - - - [14/Sep/2025:00:18:47 +0200] "GET /rsc/stat/dfi_mods_00104509.css HTTP/1.1" 200 50 "-" "Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)"
```

The access log is thus enriched with document metadata and access classification, enabling detailed usage statistics per document, session, and access type.
