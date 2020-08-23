<?php

namespace Mailamie\Emails;

use DateTime;

class Message
{
    private string $raw;
    public ?string $id;
    public string $sender;
    public array $recipients;
    public array $ccs;
    public string $htmlBody;
    public string $textBody;
    public string $subject;
    public DateTime $created_at;

    public function __construct(
        string $raw,
        string $sender,
        array $recipients,
        array $ccs,
        string $subject,
        string $htmlBody,
        string $textBody
    )
    {
        $this->raw = $raw;
        $this->sender = $sender;
        $this->recipients = $recipients;
        $this->ccs = $ccs;
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
            'ccs'        => $this->ccs,
            'html'       => $this->htmlBody,
            'text'       => $this->textBody,
            'raw'        => $this->raw,
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
