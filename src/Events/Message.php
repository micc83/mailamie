<?php

namespace Mailamie\Events;

class Message
{
    public string $body;

    public function __construct(string $body)
    {
        $this->body = $body;
    }
}
