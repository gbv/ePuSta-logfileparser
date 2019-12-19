#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$convertedLoglineParser = new ReposAS\ConvertedLoglineParser();
$filterRobots = new ReposAS\FilterRobots ();
$counter3Filter30sek = new ReposAS\Counter3Filter30sek ();

while (!feof(STDIN)) {
    if ($line = trim(fgets(STDIN))) {
        $logline = new ReposAS\ConvertedLogline();
        if ($convertedLoglineParser->parse($line, $logline)) {
            $filterRobots->edit($logline);
            $counter3Filter30sek->edit($logline);
            echo($logline . "\n");
        }
    }
}