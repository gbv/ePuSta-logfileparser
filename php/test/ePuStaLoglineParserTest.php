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

    public function testErrorE01()
    {
        // Theres a wron IP Adress in the Line
        $rawLogline = '192.168.20.110.XXX - - [06/Sep/2019:14:43:28 +0200] "GET /index.php" 200 24282 "-" "BOT1"';
        $line = '2b61e686-858b-4007-be4b-bff264f922a8 [] - [] [] [] ' . $rawLogline;
        $ePuStaLoglineParser = new ePuStaLoglineParser();
        $logline = new ePuStaLogline();
        $ePuStaLoglineParser->parse($line, $logline);
        $this->assertContains('E01', $logline->errors, 'E01 should be set when rawLogline cannot be parsed as Apache log');
    }

    public function testErrorE02()
    {
        // UUID and error array are valid, but the rest of the line is malformed (missing closing bracket)
        $line = '11111111-2222-3333-4444-555555555555 [] - ["identfier1","urn1" ["parentidentifier1"] ["tag1","tag2"] 192.168.20.110 - - [01/Sep/2000:14:43:28 +0200] "GET /url" 200 24282 "-" "Agent"';
        $ePuStaLoglineParser = new ePuStaLoglineParser();
        $logline = new ePuStaLogline();
        $ePuStaLoglineParser->parse($line, $logline);
        $this->assertContains('E02', $logline->errors, 'E02 should be set when the rest of the ePuSta line cannot be parsed');
    }

    public function testErrorE03()
    {
        // Empty line — UUID cannot be parsed
        $line = '';
        $ePuStaLoglineParser = new ePuStaLoglineParser();
        $logline = new ePuStaLogline();
        $ePuStaLoglineParser->parse($line, $logline);
        $this->assertContains('E03', $logline->errors, 'E03 should be set when UUID cannot be parsed');
    }

    public function testErrorE04()
    {
        // UUID is present but the error array field is missing (second token is not a JSON array)
        $line = '11111111-2222-3333-4444-555555555555 def1b79b ["identfier1","urn1"] ["parentidentifier1"] ["tag1","tag2"] 192.168.20.110 - - [01/Sep/2000:14:43:28 +0200] "GET /url" 200 24282 "-" "Agent"';
        $ePuStaLoglineParser = new ePuStaLoglineParser();
        $logline = new ePuStaLogline();
        $ePuStaLoglineParser->parse($line, $logline);
        $this->assertContains('E04', $logline->errors, 'E04 should be set when error array field cannot be parsed');
    }
}
