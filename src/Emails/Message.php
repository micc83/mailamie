<?php

namespace Mailamie\Emails;

use DateTime;
use Exception;

class Message
{
    private string $raw;
    public string $id;
    public string $sender;
    public array $recipients;
    public array $ccs;
    public string $htmlBody;
    public string $textBody;
    public string $subject;
    public DateTime $created_at;
    private string $replyTo;
    private array $allRecipients;
    private array $attachments;

    public function __construct(
        string $raw,
        string $sender,
        array $recipients,
        array $ccs,
        string $subject,
        string $htmlBody,
        string $textBody,
        string $replyTo,
        array $allRecipients,
        array $attachments
    )
    {
        $this->id = (string)uniqid();
        $this->raw = $raw;
        $this->sender = $sender;
        $this->recipients = $recipients;
        $this->ccs = $ccs;
        $this->htmlBody = $htmlBody;
        $this->textBody = $textBody;
        $this->subject = $subject;
        $this->created_at = new DateTime();
        $this->replyTo = $replyTo;
        $this->allRecipients = $allRecipients;
        $this->attachments = $attachments;
    }

    public function getAttachment(string $id): Attachment
    {
        $attachments = array_values(array_filter($this->attachments, function (Attachment $attachment) use ($id) {
            return $attachment->id === $id;
        }));

        if (!count($attachments)) {
            throw new Exception('Attachment not found');
        }

        return $attachments[0];
    }

    public function getExcerpt()
    {
        return mb_strimwidth(strip_tags($this->htmlBody) ?: $this->textBody, 0, 30);
    }

    public function toTable()
    {
        $table = [
            ['Date', $this->created_at->format('Y-m-d H:i:s')],
            ['Subject', "<options=bold>{$this->subject}</>"],
            ['Excerpt', $this->getExcerpt()],
            ['To', implode("; ", $this->recipients)],
            ['From', $this->sender],
        ];

        if ($this->replyTo) {
            $table[] = ['Reply-To', $this->replyTo];
        }

        if (count($this->ccs)) {
            $table[] = ['Cc', implode("; ", $this->ccs)];
        }

        $bccs = $this->getBccs();
        if (count($bccs)) {
            $table[] = ['Bcc', implode("; ", $bccs)];
        }

        $attachments = $this->getAttachmentNames();
        if (count($attachments)) {
            $table[] = ['Attachments', implode("\n", $attachments)];
        }

        return $table;
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'from'        => $this->sender,
            'reply_to'    => $this->replyTo,
            'subject'     => $this->subject,
            'recipients'  => $this->recipients,
            'ccs'         => $this->ccs,
            'bccs'        => $this->getBccs(),
            'html'        => $this->htmlBody,
            'text'        => $this->textBody,
            'raw'         => $this->raw,
            'attachments' => $this->getAttachments(),
            'created_at'  => $this->created_at->format('Y-m-d H:i:s')
        ];
    }

    private function getAttachments()
    {
        return array_map(function (Attachment $attachment) {
            return [
                'id'   => $attachment->id,
                'name' => $attachment->filename,
                'url'  => "/api/messages/{$this->id}/attachments/{$attachment->id}"
            ];
        }, $this->attachments);
    }

    private function getAttachmentNames()
    {
        return array_map(function (Attachment $attachment) {
            return $attachment->filename;
        }, $this->attachments);
    }

    /**
     * BCCs are recipients passed as RCPTs but not
     * in the body of the mail.
     * @return array
     */
    private function getBccs()
    {
        return array_values(array_filter($this->allRecipients, function (string $recipient) {
            foreach (array_merge($this->recipients, $this->ccs) as $publicRecipient) {
                if (strpos($publicRecipient, $recipient) !== false) {
                    return false;
                }
            }
            return true;
        }));
    }
}
