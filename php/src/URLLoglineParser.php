<?php

namespace epusta;

abstract class URLLoglineParser
{
    /**
     * Parse a raw log line and populate the given URLLogline object.
     * Errors encountered during parsing are appended to the $errors array
     * using codes defined in ePuStaErrors.
     *
     * @param string $rawLogline The raw log line to parse
     * @param URLLogline $logline The URLLogline object to populate
     * @param array $errors Error codes collected during parsing
     * @return bool true if parsing succeeded, false otherwise
     */
    abstract public function parse(string $rawLogline, URLLogline &$logline, array &$errors): bool;
}
