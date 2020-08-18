<?php

namespace Mailamie\Emails;

use DateTime;

class Message
{
    public ?string $id;
    public string $sender;
    public array $recipients;
    public string $htmlBody;
    public string $textBody;
    public string $subject;
    public DateTime $created_at;

    public function __construct(
        string $sender,
        array $recipients,
        string $subject,
        string $htmlBody,
        string $textBody
    )
    {
        $this->sender = $sender;
        $this->recipients = $recipients;
        $this->htmlBody = $htmlBody;
        $this->textBody = $textBody;
        $this->subject = $subject;
        $this->created_at = new DateTime();
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'from'       => $this->sender,
            'subject'    => $this->subject,
            'recipients' => $this->recipients,
            'html'       => $this->htmlBody,
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}