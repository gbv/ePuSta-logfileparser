#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$opts = getopt('h', ['help', 'debug']);
if (isset($opts['h']) || isset($opts['help'])) {
    echo "Usage: anonymize.php [OPTIONS]\n";
    echo "\n";
    echo "Reads ePuSta log lines from STDIN, anonymizes IP addresses and writes\n";
    echo "results to STDOUT.\n";
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

while (!feof(STDIN)) {
    if ($line = trim(fgets(STDIN))) {
        $logline = new epusta\ePuStaLogline();
        if ($epuStaLoglineParser->parse($line, $logline)) {
            $logline->urlLogline->anonymizeIp();
            echo($logline . "\n");
        }
    }
}
