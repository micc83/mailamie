<?php

namespace Mailamie;

use Mailamie\Events\DebugEvent;
use Mailamie\Events\Message;
use Mailamie\Events\Request;
use Mailamie\Events\Response;
use Mailamie\Events\ServerStarted;
use Psr\EventDispatcher\EventDispatcherInterface;
use React\Socket\ConnectionInterface;

class Server
{
    private string $host;
    private EventDispatcherInterface $events;

    public function __construct(string $host, EventDispatcherInterface $events)
    {
        $this->host = $host;
        $this->events = $events;
    }

    public function start()
    {
        $loop = \React\EventLoop\Factory::create();
        
        $socket = new \React\Socket\Server($this->host, $loop);

        $socket->on('connection', function (ConnectionInterface $connection) {
            $this->handleConnection($connection);
        });

        $this->events->dispatch(new ServerStarted($socket->getAddress()));

        $loop->run();
    }

    private function handleConnection(ConnectionInterface $connection)
    {
        $this->events->dispatch(new DebugEvent('New connection established with: ' . $connection->getRemoteAddress()));
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

            $this->events->dispatch(new Request($data));

            if ($data === "DATA\r\n") {
                $this->events->dispatch(new Response('354'));
                $connection->write("354\r\n");
            } elseif ($data === "QUIT\r\n") {
                $this->events->dispatch(new Response('221'));
                $connection->write("221\r\n");
                $this->events->dispatch(new DebugEvent('Connection closed with: ' .  $connection->getRemoteAddress()));
                $connection->close();
            } elseif (in_array($data, $commands)) {
                $this->events->dispatch(new Response('250'));
                $connection->write("250\r\n");
            } elseif (strpos($data, "\r\n.\r\n")) {
                $messageBody .= $data;
                $this->events->dispatch(new Response('250'));
                $this->events->dispatch(new Message($messageBody));
                $connection->write("250\r\n");
            } else {
                $messageBody .= $data;
            }
        });
    }
}
