<?php
/**
 * Created by IntelliJ IDEA.
 * User: max
 * Date: 13.11.19
 * Time: 18:17
 */

namespace epustaTest\Opus4;

use epusta\ePuStaLogline;
use epusta\ePuStaLoglineParser;
use epusta\Opus4\OpusToolbox;

class OpusToolboxTest extends \PHPUnit\Framework\TestCase
{
    private $opusToolbox;
    private $epuStaLoglineParser;
    private $testFile;

    public function setUp() : void
    {
        parent::setUp();

        $this->opusToolbox = new OpusToolbox();
        $this->epuStaLoglineParser = new ePuStaLoglineParser();

        $this->testFile = fopen(__DIR__."/../ressources/epustaLoglineWithoutIdentifiersAndSubjects.log", "r");
    }

    /**
     * Tests, if the identifier and subject of a logfile with fulltext-download in OPUS4 are correct
     */
    public function testAddIdentifierOpusFulltext()
    {
        $logline = new ePuStaLogline();

        $testline = trim(fgets($this->testFile));

        $this->epuStaLoglineParser->parse($testline, $logline);
        $this->opusToolbox->addIdentifier($logline);

        $expectedIdentifier = ["opus4-foo1-1618"];
        $expectedSubjects = ["oas:content:counter"];
        $this->assertEquals($logline->documentIdentifier, $expectedIdentifier);
        $this->assertEquals($logline->tags, $expectedSubjects);
    }

    /**
     * Tests, if the identifier and subject of a logfile with frontdoor-access in OPUS4 are correct
     */
    public function testAddIdentifierOpusFrontdoor()
    {
        $logline = new ePuStaLogline();

        while (! feof($this->testFile)) {
            $lines[] = fgets($this->testFile);
        }
        $testline = $lines[4];
        $this->epuStaLoglineParser->parse($testline, $logline);
        $this->opusToolbox->addIdentifier($logline);

        $expectedIdentifier = ["opus4-foo3-2008"];
        $expectedSubjects = ["oas:content:counter_abstract"];
        $this->assertEquals($logline->documentIdentifier, $expectedIdentifier);
        $this->assertEquals($logline->tags, $expectedSubjects);
    }
}
