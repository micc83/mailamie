<?php

namespace Mailamie;

use Mailamie\Emails\Store;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\StreamSelectLoop;
use React\Http\Server;
use React\Socket\Server as SocketServer;

class WebServer
{
    private StreamSelectLoop $loop;
    private Emails\Store $messageStore;
    private string $host;
    private string $version;

    public function __construct(string $host, string $version, StreamSelectLoop $loop, Store $messageStore)
    {
        $this->loop = $loop;
        $this->messageStore = $messageStore;
        $this->host = $host;
        $this->version = $version;
    }

    public function start(): void
    {
        $server = new Server($this->loop, function (ServerRequestInterface $request) {
            return (new WebController($this->messageStore, $this->version))
                ->route($request);
        });

        $socket = new SocketServer($this->host, $this->loop);
        $server->listen($socket);
    }
}
