<?php declare(strict_types=1);

namespace Mailamie\Emails;

use DateTimeImmutable;
use Exception;
use Mailamie\Config;

/**
 * Class Message
 * @package Mailamie\Emails
 */
class Message
{
    private string $raw;
    private string $id;
    private string $sender;
    /** @var string[] */
    private array $recipients;
    /** @var string[] */
    private array $ccs;
    private string $htmlBody;
    private string $textBody;
    private string $subject;
    private DateTimeImmutable $created_at;
    private ?string $replyTo;
    /** @var string[] */
    private array $allRecipients;
    /** @var Attachment[] */
    private array $attachments;

    /**
     * @param string[] $recipients
     * @param string[] $ccs
     * @param string[] $allRecipients
     * @param Attachment[] $attachments
     */
    public function __construct(
        string $raw,
        string $sender,
        array $recipients,
        array $ccs,
        string $subject,
        string $htmlBody,
        string $textBody,
        ?string $replyTo,
        array $allRecipients,
        array $attachments
    ) {
        $this->id = uniqid();
        $this->raw = $raw;
        $this->sender = $sender;
        $this->recipients = $recipients;
        $this->ccs = $ccs;
        $this->htmlBody = $htmlBody;
        $this->textBody = $textBody;
        $this->subject = $subject;
        $this->created_at = new DateTimeImmutable();
        $this->replyTo = $replyTo;
        $this->allRecipients = $allRecipients;
        $this->attachments = $attachments;
    }

    public function getAttachment(string $id): Attachment
    {
        $attachments = array_values(array_filter($this->attachments, function (Attachment $attachment) use ($id) {
            return $attachment->getId() === $id;
        }));

        if (!count($attachments)) {
            throw new Exception('Attachment not found');
        }

        return $attachments[0];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    /**
     * @return string[]
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @return Attachment[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @return array<array<string>>
     */
    public function toTable(): array
    {
        $table = [
            ['Date', $this->created_at->format(Config::DATE_FORMAT)],
            ['Subject', "<options=bold>{$this->subject}</>"],
            ['Excerpt', mb_strimwidth($this->textBody ?: strip_tags($this->htmlBody), 0, 200)],
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

    /**
     * @return array<string, string|null|array<string>>
     */
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
            'attachments' => $this->attachmentsToArray(),
            'created_at'  => $this->created_at->format(Config::DATE_FORMAT)
        ];
    }

    /**
     * @return array<string, string|null|array<string>>
     */
    private function attachmentsToArray(): array
    {
        return array_map(function (Attachment $attachment) {
            return [
                'id'   => $attachment->getId(),
                'name' => $attachment->getFilename(),
                'url'  => "/api/messages/{$this->id}/attachments/{$attachment->getId()}"
            ];
        }, $this->attachments);
    }

    /**
     * @return string[]
     */
    private function getAttachmentNames(): array
    {
        return array_map(function (Attachment $attachment) {
            return $attachment->getFilename();
        }, $this->attachments);
    }

    /**
     * BCCs are recipients passed as RCPTs but not
     * in the body of the mail.
     * @return string[]
     */
    private function getBccs(): array
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
