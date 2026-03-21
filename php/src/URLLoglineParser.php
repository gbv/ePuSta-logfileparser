<?php

namespace epusta;

abstract class URLLoglineParser
{
    /**
     * Parse a raw log line and populate the given URLLogline object.
     *
     * @param string $rawLogline The raw log line to parse
     * @param URLLogline $logline The URLLogline object to populate
     * @return bool true if parsing succeeded, false otherwise
     */
    abstract public function parse(string $rawLogline, URLLogline &$logline): bool;
}
