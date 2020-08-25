<?php declare(strict_types=1);

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
        $recipients = $this->joinNameAndEmail($message->getHeader('to')->getAddresses());
        $ccs = $this->joinNameAndEmail($message->getHeader('cc')->getAddresses());
        $subject = $message->getHeaderValue('subject');
        $html = $message->getHtmlContent();
        $text = $message->getTextContent();
        $replyTo = $message->getHeader('reply-to')->getRawValue();
        $attachments = $this->buildAttachmentFrom(
            $message->getAllAttachmentParts()
        );

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

    /**
     * @param MessagePart[] $attachments
     * @return Attachment[]
     */
    private function buildAttachmentFrom(array $attachments): array
    {
        return array_map(function (MessagePart $part) {
            return new Attachment(
                $part->getFilename(),
                $part->getContent(),
                $part->getContentType()
            );
        }, $attachments);
    }

    /**
     * @param AddressPart[] $addresses
     * @return string[]
     */
    private function joinNameAndEmail(array $addresses): array
    {
        return array_map(function (AddressPart $addressPart) {
            $name = $addressPart->getName();
            $email = $addressPart->getValue();

            return $name ? "{$name} <{$email}>" : $email;
        }, $addresses);
    }
}
