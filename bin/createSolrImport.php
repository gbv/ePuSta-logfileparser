#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$configuration = new \epusta\Configuration();
$config = $configuration->getConfig();
$urlLoglineParserClass = $config['URLLoglineParserClass'] ?? \epusta\ApacheLoglineParser::class;
$epuStaLoglineParser = new epusta\ePuStaLoglineParser($urlLoglineParserClass);

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
