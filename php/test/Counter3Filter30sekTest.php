<?php

namespace epustaTest;

use epusta\Counter3Filter30sek;
use epusta\ePuStaLoglineParser;
use epusta\ePuStaLogline;

class Counter3Filter30sekTest extends \PHPUnit\Framework\TestCase
{
    private $epuStaLoglineParser;
    private $counter3Filter30sek;
    private $testFile;
    
    public function setUp() : void
    {
        parent::setUp();
        $this->epuStaLoglineParser = new ePuStaLoglineParser();
        $this->counter3Filter30sek = new Counter3Filter30sek ();
        
        $logfile=__DIR__."/ressources/Counter3Filter30sekTest.log";
        $this->assertTrue(is_readable($logfile),"Fail to read file Counter3Filter30sekTest.log");
        $this->testFile = fopen($logfile , "r");
        
    }
    
    public function testAddSubjectCounter3Filter20sek()
    {
        $logline = new ePuStaLogline();
        
        $testline1 = trim(fgets($this->testFile));
        $testline2 = trim(fgets($this->testFile));
        $testline3 = trim(fgets($this->testFile));
        $testline4 = trim(fgets($this->testFile));
        $testline5 = trim(fgets($this->testFile));
        
        $this->epuStaLoglineParser->parse($testline1, $logline);
        $this->counter3Filter30sek->edit($logline);
        $this->assertNotContains("filter:30sek:counter3", $logline->tags, "Line 1 : filter:30sek:counter3 shouldn't present in subjects.");
        
        $this->epuStaLoglineParser->parse($testline2, $logline);
        $this->counter3Filter30sek->edit($logline);
        $this->assertContains("filter:30sek:counter3", $logline->tags, "Line 2 : filter:30sek:counter3 should present in subjects.");
        
        $this->epuStaLoglineParser->parse($testline3, $logline);
        $this->counter3Filter30sek->edit($logline);
        $this->assertContains("filter:30sek:counter3", $logline->tags, "Line 3 : filter:30sek:counter3 should present in subjects.");
        $count_values=array_count_values($logline->tags);
        $this->assertFalse($count_values['filter:30sek:counter3'] > 1,"Line 3 : More than one filter:30sek:counter3 shouldn't present in subjects.");
        
        
        $this->epuStaLoglineParser->parse($testline4, $logline);
        $this->counter3Filter30sek->edit($logline);
        $this->assertContains("filter:30sek:counter3", $logline->tags, "Line 4 : filter:30sek:counter3 should present in subjects.");
        
        $this->epuStaLoglineParser->parse($testline5, $logline);
        $this->counter3Filter30sek->edit($logline);
        $this->assertNotContains("filter:30sek:counter3", $logline->tags, "Line 5 : filter:30sek:counter3 should present in subjects.");
        
    }
}