#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$epuStaLoglineParser = new epusta\ePuStaLoglineParser();
$opusToolbox = new epusta\Opus4\OpusToolbox();
$opts = getopt('', ["prefix::"]);

if(array_key_exists('prefix', $opts))
{
    $prefix = $opts['prefix'];
} else {
    $prefix = NULL;
}

while (!feof(STDIN)) {
    if ($line = trim(fgets(STDIN))) {
        $logline = new epusta\ePuStaLogline();
        if ($epuStaLoglineParser->parse($line, $logline)) {
            $opusToolbox->addIdentifier($logline, $prefix);
            echo($logline . "\n");
        } else {
            // die("Error: malformed Logline" . $line . "\n")
            // TO DO Goog logging
        }
    }
}
