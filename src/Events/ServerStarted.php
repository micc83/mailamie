<?php

namespace Mailamie\Events;

class ServerStarted implements Event
{
    public string $host;

    public function __construct(string $host)
    {
        $this->host = $host;
    }
}
