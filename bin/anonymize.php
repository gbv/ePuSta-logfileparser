#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$convertedLoglineParser = new epusta\ePuStaLoglineParser();

while (!feof(STDIN)) {
    if ($line = trim(fgets(STDIN))) {
        $logline = new epusta\ePuStaLogline();
        if ($convertedLoglineParser->parse($line, $logline)) {
            $logline->urlLogline->anonymizeIp();
            echo($logline . "\n");
        }
    }
}
