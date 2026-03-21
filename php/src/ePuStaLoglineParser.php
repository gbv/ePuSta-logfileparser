<?php

namespace epusta;

class ePuStaLoglineParser
{
    private $uuidRegExp;
    private $errorsRegExp;
    private $restRegExp;
    private $urlLoglineParser;
    private $debug;

    public function __construct(string $urlLoglineParserClass = ApacheLoglineParser::class, bool $debug = false)
    {
        $this->debug = $debug;

        // Step 1: UUID (must match standard UUID format xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx)
        $this->uuidRegExp = '/^([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12})/';

        // Step 2: error array (after UUID and space)
        $this->errorsRegExp = '/^[^ ]+ (\[[^\]]*\])/';

        // Step 3: remaining fields (SessionID, DocumentIdentifier, AssociatedIdentifier, Tags, CopyOfLogline)
        $this->restRegExp = '/^[^ ]+ \[[^\]]*\] ([^ ]*) (\[[^\]]*\]) (\[[^\]]*\]) (\[[^\]]*\]) (.*)/';

        $this->urlLoglineParser = new $urlLoglineParserClass();
    }

    public function parse($line, & $logline)
    {
        $logline = new ePuStaLogline;

        // Step 1: parse UUID
        if (!preg_match($this->uuidRegExp, $line, $uuidMatch)) {
            $logline->uuid = '00000000-0000-0000-0000-000000000000';
            $logline->errors = ['E03'];
            $logline->rawLogline = $line;
            if ($this->debug) {
                fwrite(STDERR, "Error: can't parse UUID from ePuStaLogline:\n");
                fwrite(STDERR, "    " . $line . "\n");
            }
            return false;
        }

        $logline->uuid = $uuidMatch[1];

        // Step 2: parse error array
        if (!preg_match($this->errorsRegExp, $line, $errorsMatch)) {
            $logline->errors = ['E04'];
            $logline->rawLogline = $line;
            if ($this->debug) {
                fwrite(STDERR, "Error: can't parse error array from ePuStaLogline:\n");
                fwrite(STDERR, "    " . $line . "\n");
            }
            return false;
        }

        $errorsDecoded = json_decode($errorsMatch[1], true);
        if ($errorsDecoded === null) {
            $logline->errors = ['E04'];
            $logline->rawLogline = $line;
            if ($this->debug) {
                fwrite(STDERR, "Error: can't decode error array from ePuStaLogline:\n");
                fwrite(STDERR, "    " . $line . "\n");
            }
            return false;
        }

        $logline->errors = $errorsDecoded;

        // Structural errors (E02/E03/E04) mean the ePuSta line format is broken — skip step 3.
        // Non-structural errors (e.g. E01, E05) mean enrichment failed but the format is intact — proceed.
        $structuralErrors = ['E02', 'E03', 'E04'];
        if (!empty(array_intersect($logline->errors, $structuralErrors))) {
            $logline->rawLogline = $line;
            return true;
        }

        // Step 3: parse remaining fields
        if (!preg_match($this->restRegExp, $line, $restMatch)) {
            $logline->errors = ['E02'];
            $logline->rawLogline = $line;
            if ($this->debug) {
                fwrite(STDERR, "Error: can't parse ePuStaLogline:\n");
                fwrite(STDERR, "    " . $line . "\n");
                fwrite(STDERR, "    " . $this->restRegExp . "\n");
            }
            return false;
        }

        $logline->sessionId = trim($restMatch[1]);
        $logline->documentIdentifier = json_decode(trim($restMatch[2]), true);
        $logline->associatedIdentifier = json_decode(trim($restMatch[3]), true);
        $logline->tags = json_decode(trim($restMatch[4]), true);
        $logline->rawLogline = trim($restMatch[5]);

        // Skip URL parser if E01 is already present to avoid adding it twice
        if (in_array('E01', $logline->errors)) {
            return true;
        }

        $urlLogline = new URLLogline();
        $urlErrors = [];
        $this->urlLoglineParser->parse($logline->rawLogline, $urlLogline, $urlErrors);
        $logline->urlLogline = $urlLogline;

        if (!empty($urlErrors)) {
            $logline->errors = array_merge($logline->errors, $urlErrors);
        }

        return true;
    }
}
