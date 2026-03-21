#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$opts = getopt('h', ['help', 'debug', 'prefix::']);
if (isset($opts['h']) || isset($opts['help'])) {
    echo "Usage: addIdentifierOpus4.php [OPTIONS]\n";
    echo "\n";
    echo "Reads ePuSta log lines from STDIN, adds document identifiers from an OPUS4\n";
    echo "repository and writes results to STDOUT.\n";
    echo "\n";
    echo "Options:\n";
    echo "  -h, --help         Show this help message and exit\n";
    echo "  --debug            Show debug output on STDERR\n";
    echo "  --prefix=<prefix>  Repository prefix to use for identifier lookup\n";
    exit(0);
}
$debug = isset($opts['debug']);
$prefix = $opts['prefix'] ?? null;

$configuration = new \epusta\Configuration();
$config = $configuration->getConfig();
$urlLoglineParserClass = $config['URLLoglineParserClass'] ?? \epusta\ApacheLoglineParser::class;
$epuStaLoglineParser = new epusta\ePuStaLoglineParser($urlLoglineParserClass, $debug);
$opusToolbox = new epusta\Opus4\OpusToolbox();

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
