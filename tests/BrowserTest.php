<?php

namespace PhpChromiumTest;

use Mockery;
use PhpChromium\Browser;
use PhpChromium\Exceptions\OutputEmptyException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class BroserTest extends TestCase
{
    public function testDumpDom()
    {
        $dom = '<html><head><title>test</title></head><body>Welcom test.com!</body></html>';

        $browser = new BrowserForTest();
        $browser->setProcess($this->makeProcessMock($dom));

        $output = $browser->dumpDom('https://test.com');
        $this->assertSame($dom, $output);
    }

    public function testThrowsExceptionIfOutputEmpty()
    {
        $browser = new BrowserForTest();
        $browser->setProcess($this->makeProcessMock(BrowserForTest::EMPTY_DOM));

        $this->expectException(OutputEmptyException::class);
        $browser->dumpDom('https://test.com');
    }

    private function makeProcessMock(string $dom)
    {
        $mock = \Mockery::mock(Process::class);
        $mock->shouldReceive('setTimeout')->andReturn($mock);
        $mock->shouldReceive('mustRun')->andReturn($mock);
        $mock->shouldReceive('getOutput')->andReturn($dom);

        return $mock;
    }
}

class BrowserForTest extends Browser
{
    /**
     * @var Process
     */
    private $process;

    /**
     * {@inheritdoc}
     */
    protected function getProcess(array $command): Process
    {
        return $this->process;
    }

    /**
     * Set mock instance.
     *
     * @param Process $process
     * @return void
     */
    public function setProcess($process): void
    {
        $this->process = $process;
    }
}