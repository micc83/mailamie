<?php declare(strict_types=1);

namespace Mailamie\Events;

class Request implements Event
{
    public string $body;

    public function __construct(string $body)
    {
        $this->body = str_replace("\r\n", "⏎ ", $body);
    }
}
