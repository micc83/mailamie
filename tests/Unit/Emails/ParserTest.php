<?php declare(strict_types=1);

namespace Tests\Unit\Emails;

use Mailamie\Config;
use Mailamie\Emails\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /** @test */
    public function it_generated_a_message_instance_from_smtp_body_and_rcpts(): void
    {
        $parser = new Parser();

        $message = $parser->parse(
            file_get_contents(dirname(__DIR__, 2) . "/fakes/email-raw-content.eml"),
            ['bcc@example.com']
        );

        $messageData = $message->toArray();
        unset($messageData['raw']);

        $this->assertEquals([
            "id"          => $message->getId(),
            "from"        => "Mailer <from@example.com>",
            "reply_to"    => "Information <info@example.com>",
            "subject"     => "Here is the subject, welcome to New york!",
            "recipients"  => ["Joe User <joe@example.net>", "ellen@example.com"],
            "ccs"         => ["cc@example.com"],
            "bccs"        => ["bcc@example.com"],
            "html"        => "This is the HTML message body <b>in bold!</b>\n\n",
            "text"        => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\n",
            "attachments" => [
                [
                    "id"   => $message->getAttachments()[0]->getId(),
                    "name" => "vouchers.txt",
                    "url"  => "/api/messages/{$message->getId()}/attachments/{$message->getAttachments()[0]->getId()}"
                ]
            ],
            "created_at"  => $message->getCreatedAt()->format(Config::DATE_FORMAT),
        ], $messageData);
    }
}
