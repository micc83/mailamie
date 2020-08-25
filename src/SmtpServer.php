<?php declare(strict_types=1);

namespace Mailamie;

use Mailamie\Events\DebugEvent;
use Mailamie\Events\ServerStarted;
use Psr\EventDispatcher\EventDispatcherInterface;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server;

class SmtpServer
{
    private string $host;
    private EventDispatcherInterface $events;
    private LoopInterface $loop;

    public function __construct(string $host, LoopInterface $loop, EventDispatcherInterface $events)
    {
        $this->host = $host;
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
