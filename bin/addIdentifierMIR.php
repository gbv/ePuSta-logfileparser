#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$configuration = new \epusta\Configuration();
$config = $configuration->getConfig();
$convertedLoglineParser = new epusta\ePuStaLoglineParser();
$mirToolbox = new epusta\mir\MIRToolbox($config);

while (!feof(STDIN)) {
    if ($line = trim(fgets(STDIN))) {
        $logline = new epusta\ePuStaLogline();
        if ($convertedLoglineParser->parse($line, $logline)) {
            $mirToolbox->addIdentifier($logline);
            echo($logline . "\n");
        } else {
            // die("Error: malformed Logline" . $line . "\n")
            // TO DO Goog logging
        }
    }
}
