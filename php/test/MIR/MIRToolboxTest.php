<?php

namespace epustaTest;

use epusta\ePuStaLogline;
use epusta\ePuStaLoglineParser;
use epusta\mir\MIRToolbox;
use epusta\Configuration;

class MIRToolboxTest extends \PHPUnit\Framework\TestCase
{
    private $mirToolbox;
    private $convertedLoglineParser;
    private $testFile;

    public function setUp() : void
    {
        parent::setUp();
        $configuration = new Configuration();
        $config = $configuration->getPhpUnitConfig();
        $this->mirToolbox = new MIRToolbox($config);
        $this->convertedLoglineParser = new ePuStaLoglineParser();
        
        $logfile=__DIR__."/../ressources/mir-identifier-css.log";
        $this->assertTrue(is_readable($logfile),"Fail to read file mir-identifier-css.log");
        $this->testFile = fopen($logfile , "r");
         
    }

    /**
     * Because there is a failure, if there is an empty test-class, here a dummy test.
     * This class needs some ressources to be tested, so we need to wait for them.
     * Test the CSS Hack
     */
    public function testGetIdentifierFromCSSHack()
    {
        $logline = new ePuStaLogline();
        
        $testline = trim(fgets($this->testFile));
        
        $this->convertedLoglineParser->parse($testline, $logline);
        $this->mirToolbox->addIdentifier($logline);
        $this->assertContains("test_mods_00000001", $logline->documentIdentifier, "MyCoReID not parsed from css call - test_mods_00000001 is missed in array of identifier: \n".print_r($logline->identifier,true));
            
    }
}
