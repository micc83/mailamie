<?php

namespace Mailamie\Emails;

class Message {
    public string $sender;
    public array $recipients;
    public string $htmlBody;
    public string $textBody;

    public function __construct(
        string $sender, 
        array $recipients, 
        string $htmlBody,
        string $textBody
    ) {
        $this->sender = $sender;
        $this->recipients = $recipients;
        $this->htmlBody = $htmlBody;
        $this->textBody = $textBody;
    }
}