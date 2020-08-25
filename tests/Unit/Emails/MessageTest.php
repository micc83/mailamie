<?php

namespace Tests\Unit\Emails;

use Mailamie\Config;
use Mailamie\Emails\Attachment;
use PHPUnit\Framework\TestCase;
use Tests\Traits\Messages;

class MessageTest extends TestCase
{
    use Messages;

    /** @test */
    public function can_be_converted_to_array()
    {
        $message = $this->createMessage();

        $this->assertEquals([
            "id"          => $message->getId(),
            "from"        => "sender@example.com",
            "reply_to"    => "replyto@example.com",
            "subject"     => "My great subject is... 42",
            "recipients"  => ["recipient1@example.com", "recipient2@example.com"],
            "ccs"         => ["cc1@example.com", "cc2@example.com"],
            "bccs"        => ["bcc1@example.com", "bcc2@example.com"],
            "html"        => "<p>Hello World</p>",
            "text"        => "Hello World",
            "attachments" => [
                [
                    "id"   => $message->getAttachments()[0]->getId(),
                    "name" => "coupon.txt",
                    "url"  => "/api/messages/{$message->getId()}/attachments/{$message->getAttachments()[0]->getId()}"
                ]
            ],
            "created_at"  => $message->getCreatedAt()->format(Config::DATE_FORMAT),
            "raw"         => "raw content"
        ], $message->toArray());
    }

    /** @test */
    public function can_be_converted_to_cli_table()
    {
        $message = $this->createMessage();

        $this->assertEquals([
            ['Date', $message->getCreatedAt()->format(Config::DATE_FORMAT)],
            ['Subject', '<options=bold>My great subject is... 42</>'],
            ['Excerpt', 'Hello World'],
            ['To', 'recipient1@example.com; recipient2@example.com'],
            ['From', 'sender@example.com'],
            ['Reply-To', 'replyto@example.com'],
            ['Cc', 'cc1@example.com; cc2@example.com'],
            ['Bcc', 'bcc1@example.com; bcc2@example.com'],
            ['Attachments', 'coupon.txt']
        ], $message->toTable());
    }

    /** @test */
    public function it_allows_to_retrieve_an_attachment_by_id()
    {
        $message = $this->createMessage();
        $attachmentId = $message->getAttachments()[0]->getId();

        $this->assertInstanceOf(Attachment::class, $message->getAttachment($attachmentId));
    }
}
