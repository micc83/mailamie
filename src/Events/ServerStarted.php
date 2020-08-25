<?php declare(strict_types=1);

namespace Mailamie\Events;

class ServerStarted implements Event
{
    public string $host;

    public function __construct(string $host)
    {
        $this->host = $host;
    }
}
