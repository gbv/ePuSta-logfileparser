<?php

namespace epustaTest;

use epusta\ePuStaLogline;
use epusta\ePuStaLoglineParser;
use epusta\mir\MIRToolbox;
use epusta\Configuration;

class MIRToolboxTest extends \PHPUnit\Framework\TestCase
{
    private $mirToolbox;
    private $epuStaLoglineParser;
    private $testFile;

    public function setUp() : void
    {
        parent::setUp();
        $configuration = new Configuration();
        $config = $configuration->getPhpUnitConfig();
        $this->mirToolbox = new MIRToolbox($config);
        $this->epuStaLoglineParser = new ePuStaLoglineParser();
        
        
    }

    /**
     * Because there is a failure, if there is an empty test-class, here a dummy test.
     * This class needs some ressources to be tested, so we need to wait for them.
     * Test the CSS Hack
     */
    public function testGetIdentifierFromCSSHack()
    {
        $testline = '03147e0f-ac4e-4163-a93d-d1730eaf8c1a [] - [] [] [] 193.174.111.250 - - [08/Apr/2019:13:13:09 +0200] "GET /rsc/stat/test_mods_00000001.css HTTP/1.1" 200 61 "" ""';
        $this->epuStaLoglineParser->parse($testline, $logline);
        $this->mirToolbox->addIdentifier($logline);
        $this->assertContains("test_mods_00000001", $logline->documentIdentifier, "MyCoReID not parsed from xml file - test_mods_00000001 is missed in array of identifier: \n".print_r($logline->documentIdentifier,true));
        $this->assertContains("urn:nbn:de:test:1-1", $logline->documentIdentifier, "URN not parsed from xml file - urn:nbn:de:test:1-1 is missed in array of identifier: \n".print_r($logline->documentIdentifier,true));
        $this->assertContains("11111/11111-0", $logline->documentIdentifier, "DOI not parsed from xml file - 11111/11111-0 is missed in array of identifier: \n".print_r($logline->documentIdentifier,true));
        
        
    }
}
