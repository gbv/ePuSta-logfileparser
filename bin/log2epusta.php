#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$opts = getopt('h', ['help', 'debug']);
if (isset($opts['h']) || isset($opts['help'])) {
    echo "Usage: log2epusta.php [OPTIONS]\n";
    echo "\n";
    echo "Reads raw web server log lines from STDIN and converts them to ePuSta log\n";
    echo "format, writing results to STDOUT.\n";
    echo "\n";
    echo "Options:\n";
    echo "  -h, --help   Show this help message and exit\n";
    echo "  --debug      Show debug output on STDERR\n";
    exit(0);
}

$configuration = new \epusta\Configuration();
$config = $configuration->getConfig();

//use Monolog\Handler\StreamHandler;
//use Monolog\Logger;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;
use epusta\ePuStaLogline;

//$logger = new Logger('log2epusta');
//$logger->pushHandler(new StreamHandler($config['logdir'] . '/log2epusta.log', Logger::DEBUG));


while (!feof(STDIN)) {
    if ($line = trim(fgets(STDIN))) {
        $logline = new ePuStaLogline();
        try {
            $logline->uuid = Uuid::uuid4();
        } catch (UnsatisfiedDependencyException $e) {
            // Some dependency was not met. Either the method cannot be called on a
            // 32-bit system, or it can, but it relies on Moontoast\Math to be present.
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }

        echo($logline->convertLogline($line) . "\n");
    }
}
