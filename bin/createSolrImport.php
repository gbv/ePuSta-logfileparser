#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$opts = getopt('h', ['help', 'debug']);
if (isset($opts['h']) || isset($opts['help'])) {
    echo "Usage: createSolrImport.php [OPTIONS]\n";
    echo "\n";
    echo "Reads ePuSta log lines from STDIN and writes Solr import JSON to STDOUT.\n";
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
            $str = '{ "uuid": "' . $logline->uuid . '"';
            $str .= ', "identifier":' . json_encode($logline->documentIdentifier);
            $time = new DateTime($logline->urlLogline->time);
            $str .= ', "dateTime":"' . $time->format(DateTime::ISO8601) . '"';
            $str .= ', "subjects":' . json_encode($logline->tags);
            $str .= '}';
            echo $str . "\n";
        } else {
            // die("Error: malformed Logline - abort Processing.\n");
        }
    }
}
