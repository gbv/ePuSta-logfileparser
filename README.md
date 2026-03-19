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

### Target Format (epustalog)

Each parsed and enriched line has the following structure:

```
<uuid> <original log line> <sessionId> <identifierArray> <subjectsArray>
```

The fields appended after the original log line are separated by spaces:

| Field | Description |
|-------|-------------|
| `uuid` | A UUID (v4) prepended to the line, uniquely identifying this log entry |
| `original log line` | An unmodified copy of the original source log line |
| `sessionId` | A session identifier for the requesting client (or `-` if unknown) |
| `identifierArray` | A JSON array of identifiers associated with the accessed document |
| `subjectsArray` | A JSON array of tags/keywords describing the type of access or document properties |

#### Identifier Array

The identifier array contains identifiers associated with the accessed document. These include:

- Identifiers of the document itself (e.g. MyCoRe ID, DOI, GND)
- Identifiers of parent/containing units (e.g. the journal or series a document belongs to)

Example: `["dfi_mods_00104509","dfi_mods_00000038"]`

#### Subjects Array

The subjects array contains tags that describe:

- The type of access (e.g. whether the request was made by a bot)
- Properties of the document (e.g. genre, content type, OA status)

Common tag prefixes:

| Prefix | Description |
|--------|-------------|
| `filter:` | Access filter classification (e.g. `filter:robot`) |
| `mir_genre:` | Document genre from MIR/MyCoRe (e.g. `mir_genre:article`) |
| `oas:` | Open Access and content type classification (e.g. `oas:content:robots_abstract`) |

Example: `["mir_genre:article","filter:robot","oas:content:robots_abstract"]`

### Complete Example

Source line (Apache Combined Log Format):

```
- - - [14/Sep/2025:00:18:47 +0200] "GET /rsc/stat/dfi_mods_00104509.css HTTP/1.1" 200 50 "-" "Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)"
```

Resulting epustalog line:

```
11b42b8c-fa92-49ec-9856-d398a731aecf - - - [14/Sep/2025:00:18:47 +0200] "GET /rsc/stat/dfi_mods_00104509.css HTTP/1.1" 200 50 "-" "Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)" - ["dfi_mods_00104509","dfi_mods_00000038"] ["mir_genre:article","filter:robot","oas:content:robots_abstract"]
```

The access log is thus enriched with document metadata and access classification, enabling detailed usage statistics per document, session, and access type.
