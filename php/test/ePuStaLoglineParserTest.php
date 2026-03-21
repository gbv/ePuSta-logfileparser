<?php

namespace epustaTest;

use epusta\ePuStaLogline;
use epusta\ePuStaLoglineParser;

class ePuStaLoglineParserTest extends \PHPUnit\Framework\TestCase
{
    public function testParse()
    {
        $testFile = fopen(__DIR__."/ressources/epustaLoglineWithoutIdentifiersAndSubjects.log", "r");
        $testline = trim(fgets($testFile));
        $ePuStaLoglineParser = new ePuStaLoglineParser();
        $logline = new ePuStaLogline();
        $ePuStaLoglineParser->parse($testline, $logline);
        $this->assertEquals($testline, $logline->__toString());
    }

    public function testErrorE01WhenRawLoglineCannotBeParsed()
    {
        $line = '2b61e686-858b-4007-be4b-bff264f922a8 - [] [] [] [] this is not an apache log line';
        $ePuStaLoglineParser = new ePuStaLoglineParser();
        $logline = new ePuStaLogline();
        $ePuStaLoglineParser->parse($line, $logline);
        $this->assertContains('E01', $logline->errors, 'E01 should be set when rawLogline cannot be parsed as Apache log');
    }

    public function testErrorE02WhenEpustaFormatCannotBeParsed()
    {
        $line = 'this is not an epusta logline';
        $ePuStaLoglineParser = new ePuStaLoglineParser();
        $logline = new ePuStaLogline();
        $ePuStaLoglineParser->parse($line, $logline);
        $this->assertContains('E02', $logline->errors, 'E02 should be set when line does not match ePuSta format');
    }
}
