<?php

namespace Mailamie;

use Mailamie\Emails\Store;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\StreamSelectLoop;
use React\Http\Message\Response;
use React\Http\Server;
use React\Socket\Server as SocketServer;
use Throwable;

class WebServer
{
    private StreamSelectLoop $loop;
    private Emails\Store $messageStore;
    private string $host;

    public function __construct(string $host, StreamSelectLoop $loop, Store $messageStore)
    {
        $this->loop = $loop;
        $this->messageStore = $messageStore;
        $this->host = $host;
    }

    public function start(): void
    {
        $server = new Server($this->loop, function (ServerRequestInterface $request) {
            return (new WebController($this->messageStore))
                ->route($request);
        });

        $socket = new SocketServer($this->host, $this->loop);
        $server->listen($socket);
    }
}
