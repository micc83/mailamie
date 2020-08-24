<?php

namespace Mailamie\Emails;

use ZBateson\MailMimeParser\Header\Part\AddressPart;
use ZBateson\MailMimeParser\Message as ParseMessage;
use ZBateson\MailMimeParser\Message\Part\MessagePart;

class Parser
{
    /**
     * @param string $rawContent
     * @param string[] $allRecipients
     * @return Message
     */
    public function parse(string $rawContent, array $allRecipients = []): Message
    {
        $message = ParseMessage::from($rawContent);

        $from = $message->getHeader('from')->getRawValue();

        $recipients = array_map(function (AddressPart $addressPart) {
            $name = $addressPart->getName();
            $email = $addressPart->getValue();
            if ($name) {
                return "{$name} <{$email}>";
            }
            return $email;
        }, $message->getHeader('to')->getAddresses());

        $ccs = array_map(function (AddressPart $addressPart) {
            $name = $addressPart->getName();
            $email = $addressPart->getValue();
            if ($name) {
                return "{$name} <{$email}>";
            }
            return $email;
        }, $message->getHeader('cc')->getAddresses());

        $subject = $message->getHeaderValue('subject');

        $html = $message->getHtmlContent();
        $text = $message->getTextContent();

        $replyTo = $message->getHeader('reply-to')->getRawValue();

        $attachments = [];
        foreach ($message->getAllAttachmentParts() as $part) {
            $attachments[] = new Attachment(
                $part->getFilename(),
                $part->getContent(),
                $part->getContentType()
            );
        }

        return new Message(
            $rawContent,
            $from,
            $recipients,
            $ccs,
            $subject,
            $html,
            $text,
            $replyTo,
            $allRecipients,
            $attachments
        );
    }
}
