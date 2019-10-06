<?php

namespace PhpChromium\Exceptions;

use Exception;

class OutputEmptyException extends Exception
{
    /**
     * @var string
     */
    private $url;

    public function __construct(string $url, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->url = $url;
        parent::__construct($message, $code, $previous);
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}