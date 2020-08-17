<?php

namespace Mailamie;

use Mailamie\Events\DebugEvent;
use Mailamie\Events\ServerStarted;
use Psr\EventDispatcher\EventDispatcherInterface;
use React\EventLoop\StreamSelectLoop;
use React\Socket\ConnectionInterface;
use React\Socket\Server;

class SmtpServer
{
    private string $host;
    private EventDispatcherInterface $events;

    const DEFAULT_HOST = '127.0.0.1:8025';
    /**
     * @var StreamSelectLoop
     */
    private StreamSelectLoop $loop;

    public function __construct(string $host, StreamSelectLoop $loop, EventDispatcherInterface $events)
    {
        $this->host = $host ?: static::DEFAULT_HOST;
        $this->events = $events;
        $this->loop = $loop;
    }

    public function start(): void
    {
        $socket = new Server($this->host, $this->loop);

        $socket->on('connection', function (ConnectionInterface $connection) {
            $this->handleConnection($connection);
        });

        $this->events->dispatch(new ServerStarted($socket->getAddress()));
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
