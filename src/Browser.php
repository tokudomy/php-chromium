<?php

namespace PhpChromium;

use PhpChromium\Exceptions\OutputEmptyException;
use Symfony\Component\Process\Process;

class Browser
{
    const EMPTY_DOM = '<html><head></head><body></body></html>';

    /**
     * Chromium or Chrome command.
     *
     * @var string
     */
    private $chromium_command;

    /**
     * Constructor
     *
     * @param string $chromium_command
     */
    public function __construct(string $chromium_command = null)
    {
        if ($chromium_command === null) {
            $chromium_command = 'chromium-browser';
        }

        $this->chromium_command = $chromium_command;
    }

    /**
     * @param string $url
     * @param int $timeout_ms
     * @return string
     * @throws OutputEmptyException
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     * @throws \Symfony\Component\Process\Exception\ProcessTimedOutException
     */
    public function dumpDom(string $url, int $timeout_ms = 30000): string
    {
        $process = $this->getProcess($this->getCommand($url));
        $process->setTimeout($timeout_ms / 1000);

        $dom = $process->mustRun()->getOutput();
        unset($process);

        if ($this->isEmptyDom($dom)) {
            throw new OutputEmptyException($url);
        }

        return $dom;
    }

    /**
     * @param string $dom
     * @return boolean
     */
    private function isEmptyDom(string $dom): bool
    {
        return trim($dom) === self::EMPTY_DOM;
    }

    /**
     * @param string $url
     * @return array
     */
    private function getCommand(string $url): array
    {
        $options = [
            '--headless',
            '--disable-gpu',
            '--dump-dom',
        ];

        return array_merge([$this->chromium_command, $url], $options);
    }

    /**
     * @param array $command
     * @return Process
     */
    protected function getProcess(array $command): Process
    {
        return new Process($command);
    }
}
