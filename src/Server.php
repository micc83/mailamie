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

    public function start(): void
    {
        $loop = \React\EventLoop\Factory::create();

        $socket = new \React\Socket\Server($this->host, $loop);

        $socket->on('connection', function (ConnectionInterface $connection) {
            $this->handleConnection($connection);
        });

        $this->events->dispatch(new ServerStarted($socket->getAddress()));

        $loop->run();
    }

    private function handleConnection(ConnectionInterface $connection): void
    {
        $remoteAddress = $connection->getRemoteAddress();

        $this->events->dispatch(
            new DebugEvent("New connection established with: {$remoteAddress}")
        );

        $smtp = new SmtpConnection($connection, $this->events);

        $smtp->ready();

        $connection->on('data', function ($data) use ($smtp) {
            $smtp->handle($data);
        });
    }
}
