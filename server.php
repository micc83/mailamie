<?php

use Mailamie\Server;
use React\Socket\ConnectionInterface;

require 'vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$socket = new React\Socket\Server(8025, $loop);

function dumpRequest(string $data, bool $inline = true, string $responseData = null): void
{

    if (!$inline) {
        $requestData = "\n----------------------------------------------\n";
        $requestData .= str_replace("\r\n", "⏎\n", $data);
    } else {
        $requestData = str_replace("\r\n", "⏎", $data);
    }

    echo "\n----------------------------------------------\n";
    echo "> REQUEST: {$requestData}";
    echo "\n----------------------------------------------\n";

    if ($responseData) {
        echo "\n\t|-----------------------\n";
        echo "\t|> RESPONSE: {$responseData}";
        echo "\n\t|-----------------------\n";
    }
}

function parseMessageBody(string $content): void
{
    $mailParser = new \ZBateson\MailMimeParser\MailMimeParser();
    $message = \ZBateson\MailMimeParser\Message::from($content);

    echo "\n\t|-----------------------\n";
    echo "\t| PARSED MESSAGE";
    echo "\n\t| Recipient: " . $message->getHeader('to')->getAddresses()[0]->getEmail();
    echo "\n\t| Text content: " . $message->getTextContent();
    echo "\n\t| Html content: " . $message->getHtmlContent();
    echo "\n\t|-----------------------\n";

    
}

$socket->on('connection', function (ConnectionInterface $connection) {
    $messageBody = false;

    echo "\n----------------------------------------------\n";
    echo "> CONNECTED...";
    echo "\n----------------------------------------------\n";

    $connection->write('250');
    $connection->on('data', function ($data) use ($connection, &$messageBody) {
        $commands = [
            "EHLO DESKTOP-4PPP4Q6\r\n",
            "MAIL FROM:<from@example.com>\r\n",
            "RCPT TO:<joe@example.net>\r\n",
            "RCPT TO:<ellen@example.com>\r\n",
            "RCPT TO:<cc@example.com>\r\n",
            "RCPT TO:<bcc@example.com>\r\n",
        ];

        if ($data === "DATA\r\n") {
            dumpRequest($data, false, "354");
            $connection->write("354\r\n");
        } elseif ($data === "QUIT\r\n") {
            dumpRequest($data, true, "221");
            $connection->write("221\r\n");
        } elseif (in_array($data, $commands)) {
            dumpRequest($data, true, "250");
            $connection->write("250\r\n");
        } elseif (strpos($data, "\r\n.\r\n")) {
            $messageBody .= $data;
            dumpRequest($data, true, "250");
            parseMessageBody($messageBody);
            $connection->write("250\r\n");
        } else {
            $messageBody .= $data;
            // No response  
            dumpRequest($data, true);
        }
    });
});

echo "Listening on {$socket->getAddress()}\n";

$loop->run();
