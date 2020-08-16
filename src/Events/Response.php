<?php

namespace Mailamie\Events;

class Response
{
    public string $body;

    public function __construct(string $body)
    {
        $this->body = $body;
    }
}
