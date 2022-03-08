<?php declare(strict_types=1);

namespace Mailamie\Emails;

use ZBateson\MailMimeParser\Header\AbstractHeader;
use ZBateson\MailMimeParser\Header\AddressHeader;
use ZBateson\MailMimeParser\Header\Part\AddressPart;
use ZBateson\MailMimeParser\Message as ParseMessage;
use ZBateson\MailMimeParser\Message\IMessagePart;

class Parser
{
    /**
     * @param string $rawContent
     * @param string[] $allRecipients
     * @return Message
     */
    public function parse(string $rawContent, array $allRecipients = []): Message
    {
        $message = ParseMessage::from($rawContent, true);

        $from = $message->getHeader('from')->getRawValue();
        /** @var AddressHeader|null $toHeader */
        $toHeader = $message->getHeader('to');
        $recipients = $this->joinNameAndEmail($toHeader ? $toHeader->getAddresses() : []);
        /** @var AddressHeader|null $ccHeader */
        $ccHeader = $message->getHeader('cc');
        $ccs = $this->joinNameAndEmail($ccHeader ? $ccHeader->getAddresses() : []);
        $subject = (string) $message->getHeaderValue('subject');
        $html = (string) $message->getHtmlContent();
        $text = (string) $message->getTextContent();
        /** @var AbstractHeader|null $replyToHeader */
        $replyToHeader = $message->getHeader('reply-to');
        $replyTo = $replyToHeader ? $replyToHeader->getRawValue() : null;
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
     * @param IMessagePart[] $attachments
     * @return Attachment[]
     */
    private function buildAttachmentFrom(array $attachments): array
    {
        return array_map(function (IMessagePart $part) {
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
