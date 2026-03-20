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
        $epuStaLoglineParser = new ePuStaLoglineParser();
        $logline = new ePuStaLogline();
        $epuStaLoglineParser->parse($testline, $logline);
        $this->assertEquals($testline, $logline->__toString());
    }
}
