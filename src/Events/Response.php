<?php

namespace Mailamie\Events;

class Response
{
    public string $body;
    public string $code;

    public function __construct(int $code, string $body)
    {
        $this->code = (string) $code;
        $this->body = $body;
    }
}
