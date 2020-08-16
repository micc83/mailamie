<?php

namespace Mailamie\Emails;

use ZBateson\MailMimeParser\Header\Part\AddressPart;

class Parser {
    public function parse(string $content): Message
    {
        $mailParser = new \ZBateson\MailMimeParser\MailMimeParser();
        $message = \ZBateson\MailMimeParser\Message::from($content);

        return new Message(
            $message->getHeader('from')->getRawValue(),
            array_map(function (AddressPart $addressPart) {
                $name = $addressPart->getName();
                $email = $addressPart->getValue();
                if ($name){
                    return "{$name} <{$email}>";
                }
                return $email;
            }, $message->getHeader('to')->getAddresses()),
            $message->getHeaderValue('subject'),
            $message->getHtmlContent(),
            $message->getTextContent(),
        );
    }
}