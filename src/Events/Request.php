<?php

namespace Mailamie\Events;

class Request
{
    public string $body;

    public function __construct(string $body)
    {
        $this->body = str_replace("\r\n", "⏎", $body);
    }
}
