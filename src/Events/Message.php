<?php

namespace Mailamie\Events;

class Message implements Event
{
    public string $body;

    /**
     * @var string[] $recipients
     */
    public array $recipients;

    /**
     * @param string $body
     * @param string[] $recipients
     */
    public function __construct(string $body, array $recipients)
    {
        $this->body = $body;
        $this->recipients = $recipients;
    }
}
