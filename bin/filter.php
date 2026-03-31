#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$opts = getopt('h', ['help', 'debug']);
if (isset($opts['h']) || isset($opts['help'])) {
    echo "Usage: filter.php [OPTIONS]\n";
    echo "\n";
    echo "Reads ePuSta log lines from STDIN, applies filters (robots, HTTP status,\n";
    echo "HTTP method, 30-second counter deduplication) and writes results to STDOUT.\n";
    echo "\n";
    echo "Options:\n";
    echo "  -h, --help   Show this help message and exit\n";
    echo "  --debug      Show debug output on STDERR\n";
    exit(0);
}
$debug = isset($opts['debug']);

$configuration = new \epusta\Configuration();
$config = $configuration->getConfig();
$urlLoglineParserClass = $config['URLLoglineParserClass'] ?? \epusta\ApacheLoglineParser::class;
$epuStaLoglineParser = new epusta\ePuStaLoglineParser($urlLoglineParserClass, $debug);
$filterRobots = new epusta\FilterRobots ();
$counter3Filter30sek = new epusta\Counter3Filter30sek ();
$filterHttpStatus = new \epusta\FilterHttpStatus();
$filterHttpMethod = new \epusta\FilterHttpMethod();

while (!feof(STDIN)) {
    if ($line = trim(fgets(STDIN))) {
        $logline = new epusta\ePuStaLogline();
        if ($epuStaLoglineParser->parse($line, $logline)) {
            if (!$logline->hasErrors()) {
                $filterRobots->edit($logline);
                $counter3Filter30sek->edit($logline);
                $filterHttpStatus->edit($logline);
                $filterHttpMethod->edit($logline);
            }
            echo($logline . "\n");
        } else {
            // die("Error: malformed Logline" . $line . "\n")
            // TO DO Goog logging
        }
    }
}
