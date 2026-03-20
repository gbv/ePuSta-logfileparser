#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$convertedLoglineParser = new epusta\ePuStaLoglineParser();
$filterRobots = new epusta\FilterRobots ();
$counter3Filter30sek = new epusta\Counter3Filter30sek ();
$filterHttpStatus = new \epusta\FilterHttpStatus();
$filterHttpMethod = new \epusta\FilterHttpMethod();

while (!feof(STDIN)) {
    if ($line = trim(fgets(STDIN))) {
        $logline = new epusta\ePuStaLogline();
        if ($convertedLoglineParser->parse($line, $logline)) {
            $filterRobots->edit($logline);
            $counter3Filter30sek->edit($logline);
            $filterHttpStatus->edit($logline);
            $filterHttpMethod->edit($logline);
            echo($logline . "\n");
        } else {
            // die("Error: malformed Logline" . $line . "\n")
            // TO DO Goog logging
        }
    }
}
