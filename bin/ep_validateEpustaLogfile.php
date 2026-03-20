#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$onlyFirstLine = false;
$file = null;

$args = array_slice($argv, 1);
$i = 0;
while ($i < count($args)) {
    $arg = $args[$i];
    if ($arg === '-h' || $arg === '--help') {
        echo "Usage: ep_validateEpustaLogfile.php [OPTIONS] <file>\n";
        echo "\n";
        echo "Checks whether a file is a valid ePuSta log file.\n";
        echo "Each line is validated against the ePuSta log line format.\n";
        echo "\n";
        echo "Options:\n";
        echo "  -h, --help            Show this help message and exit\n";
        echo "  --only-first-line     Only validate the first line of the file\n";
        echo "\n";
        echo "Exit codes:\n";
        echo "  0  All checked lines are valid\n";
        echo "  1  One or more lines are invalid\n";
        exit(0);
    } elseif ($arg === '--only-first-line') {
        $onlyFirstLine = true;
    } else {
        $file = $arg;
    }
    $i++;
}

if ($file === null) {
    fwrite(STDERR, "Error: no file specified\n");
    fwrite(STDERR, "Usage: ep_validateEpustaLogfile.php [OPTIONS] <file>\n");
    fwrite(STDERR, "Try 'ep_validateEpustaLogfile.php --help' for more information.\n");
    exit(1);
}

if (!is_readable($file)) {
    fwrite(STDERR, "Error: cannot read file '$file'\n");
    exit(1);
}

$epuStaLoglineParser = new epusta\ePuStaLoglineParser();
$handle = fopen($file, 'r');
$valid = true;
$lineNumber = 0;

while (!feof($handle)) {
    $line = fgets($handle);
    if ($line === false) {
        break;
    }
    $line = trim($line);
    if ($line === '') {
        continue;
    }
    $lineNumber++;
    $logline = new epusta\ePuStaLogline();
    if (!$epuStaLoglineParser->parse($line, $logline)) {
        fwrite(STDERR, "Invalid line $lineNumber in '$file'\n");
        $valid = false;
    }
    if ($onlyFirstLine) {
        break;
    }
}

fclose($handle);

exit($valid ? 0 : 1);
