<?php declare(strict_types=1);

namespace Mailamie\Events;

class Response implements Event
{
    public string $body;
    public string $code;

    public function __construct(int $code, string $body, string $comment = '')
    {
        $this->code = "{$code} {$comment}";
        $this->body = $body;
    }
}
