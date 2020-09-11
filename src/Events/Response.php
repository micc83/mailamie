<?php declare(strict_types=1);

namespace Mailamie\Events;

class Response implements Event
{
    public string $body;
    public string $code;
    private ?string $comment;

    public function __construct(int $code, string $body, string $comment = null)
    {
        $this->code = (string)$code;
        $this->body = $body;
        $this->comment = $comment;
    }
}
