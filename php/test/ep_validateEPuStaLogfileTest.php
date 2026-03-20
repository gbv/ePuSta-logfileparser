<?php

namespace epustaTest;

class ep_validateEPuStaLogfileTest extends \PHPUnit\Framework\TestCase
{
    private $script;
    private $validLogfile;
    private $invalidLogfile;

    public function setUp(): void
    {
        parent::setUp();
        $this->script = __DIR__ . '/../../bin/ep_validateEpustaLogfile.php';
        $this->validLogfile = __DIR__ . '/ressources/epustaLoglineWithoutIdentifiersAndSubjects.log';
        $this->invalidLogfile = __DIR__ . '/ressources/invalidEpustaLogfile.log';

        file_put_contents($this->invalidLogfile, "this is not a valid epusta log line\n");
    }

    public function tearDown(): void
    {
        if (file_exists($this->invalidLogfile)) {
            unlink($this->invalidLogfile);
        }
    }

    public function testValidLogfileReturnsExitCode0()
    {
        exec('php ' . escapeshellarg($this->script) . ' ' . escapeshellarg($this->validLogfile), $output, $exitCode);
        $this->assertEquals(0, $exitCode, "Valid logfile should exit with code 0");
    }

    public function testInvalidLogfileReturnsExitCode1()
    {
        exec('php ' . escapeshellarg($this->script) . ' ' . escapeshellarg($this->invalidLogfile) . ' 2>/dev/null', $output, $exitCode);
        $this->assertEquals(1, $exitCode, "Invalid logfile should exit with code 1");
    }

    public function testOnlyFirstLineOptionWithValidFirstLine()
    {
        exec('php ' . escapeshellarg($this->script) . ' --only-first-line ' . escapeshellarg($this->validLogfile), $output, $exitCode);
        $this->assertEquals(0, $exitCode, "--only-first-line on valid file should exit with code 0");
    }

    public function testOnlyFirstLineOptionWithInvalidFirstLine()
    {
        exec('php ' . escapeshellarg($this->script) . ' --only-first-line ' . escapeshellarg($this->invalidLogfile) . ' 2>/dev/null', $output, $exitCode);
        $this->assertEquals(1, $exitCode, "--only-first-line on invalid file should exit with code 1");
    }

    public function testHelpOptionExitsWithCode0()
    {
        exec('php ' . escapeshellarg($this->script) . ' --help', $output, $exitCode);
        $this->assertEquals(0, $exitCode, "--help should exit with code 0");
    }

    public function testHelpOutputContainsUsage()
    {
        exec('php ' . escapeshellarg($this->script) . ' --help', $output, $exitCode);
        $outputStr = implode("\n", $output);
        $this->assertStringContainsString('Usage', $outputStr);
        $this->assertStringContainsString('--only-first-line', $outputStr);
    }

    public function testMissingFileArgumentReturnsExitCode1()
    {
        exec('php ' . escapeshellarg($this->script) . ' 2>/dev/null', $output, $exitCode);
        $this->assertEquals(1, $exitCode, "Missing file argument should exit with code 1");
    }

    public function testNonExistentFileReturnsExitCode1()
    {
        exec('php ' . escapeshellarg($this->script) . ' /nonexistent/file.log 2>/dev/null', $output, $exitCode);
        $this->assertEquals(1, $exitCode, "Non-existent file should exit with code 1");
    }
}
