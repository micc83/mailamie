<?php declare(strict_types=1);

namespace Mailamie\Events;

class Response implements Event
{
    public string $body;
    public string $code;

    public function __construct(int $code, string $body)
    {
        $this->code = (string)$code;
        $this->body = $body;
    }
}
