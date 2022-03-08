<?php declare(strict_types=1);

namespace Tests\Traits;

use Mailamie\Emails\Attachment;
use Mailamie\Emails\Message;

trait Messages
{
    /**
     * @param array<string|array<string>> $override
     * @return Message
     */
    private function createMessage(array $override = []): Message
    {
        $params = array_merge([
            'raw'            => "raw content",
            'sender'         => "sender@example.com",
            'recipients'     => ["recipient1@example.com", "recipient2@example.com"],
            'ccs'            => ["cc1@example.com", "cc2@example.com"],
            'subject'        => "My great subject is... 42",
            'html'           => "<p>Hello World</p>",
            'text'           => "Hello World",
            'reply_to'       => "replyto@example.com",
            'all_recipients' => [
                "recipient1@example.com",
                "recipient2@example.com",
                "cc1@example.com",
                "cc2@example.com",
                "bcc1@example.com",
                "bcc2@example.com"
            ],
            'attachments'    => [
                new Attachment("coupon.txt", "My coupont content", "text/plain")
            ]
        ], $override);

        return new Message(...array_values($params));
    }
}
