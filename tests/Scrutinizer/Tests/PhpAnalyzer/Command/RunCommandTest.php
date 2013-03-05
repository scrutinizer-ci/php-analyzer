<?php

namespace Scrutinizer\Tests\PhpAnalyzer\Command;

use Scrutinizer\Process\Process;

class RunCommandTest extends \PHPUnit_Framework_TestCase
{
    private $proc;

    public function testRun()
    {
        $this->runCommand(__DIR__.'/Fixture/TestProject');
        $this->assertOutputContains('The variable ``$x`` does not exist. Did you forget to declare it?');
        $this->assertOutputNotContains('used types were not defined');
    }

    public function testFilter()
    {
        $this->runCommand(__DIR__.'/Fixture/TestProject --filter-pattern="foobar"');
        $this->assertOutputNotContains('The variable ``$x`` does not exist. Did you forget to declare it?');
    }

    /**
     * @group partial-project
     */
    public function testDisplaysWarningWhenAnalyzingPartialProject()
    {
        $this->runCommand(__DIR__.'/Fixture/PartialProject');
        $this->assertOutputContains('83.33% of used types were not defined');
    }

    private function assertOutputNotContains($str)
    {
        $this->assertNotNull($this->proc);
        $this->assertNotContains($str, $this->proc->getOutput());
    }

    private function assertOutputContains($str)
    {
        $this->assertNotNull($this->proc);
        $this->assertContains($str, $this->proc->getOutput());
    }

    private function runCommand($argStr)
    {
        $this->proc = new Process('php bin/phpalizer run '.$argStr, ROOT_DIR);
        $this->proc->runOrException();
    }
}