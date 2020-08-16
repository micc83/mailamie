<?php

namespace Mailamie\Events;

class ServerStarted
{
    public string $host;

    public function __construct(string $host)
    {
        $this->host = $host;
    }
}
