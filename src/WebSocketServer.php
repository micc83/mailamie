<?php

namespace Mailamie;

use Mailamie\Emails\Store;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\StreamSelectLoop;
use React\Socket\Server;

class WebSocketServer
{
    private StreamSelectLoop $loop;
    private Store $store;
    private string $host;

    public function __construct(string $host, StreamSelectLoop $loop, Store $store)
    {
        $this->loop = $loop;
        $this->store = $store;
        $this->host = $host;
    }

    public function start()
    {
        new IoServer(
            new HttpServer(
                new WsServer(
                    new WebSocketComponent($this->store)
                )
            ),
            new Server($this->host, $this->loop),
            $this->loop
        );
    }
}
