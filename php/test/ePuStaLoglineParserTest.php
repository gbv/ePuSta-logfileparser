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
        $convertedLoglineParser = new ePuStaLoglineParser();
        $logline = new ePuStaLogline();
        $convertedLoglineParser->parse($testline, $logline);
        $this->assertEquals($testline, $logline->__toString());
    }
}
