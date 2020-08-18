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

    public function __construct(StreamSelectLoop $loop, Store $store)
    {
        $this->loop = $loop;
        $this->store = $store;
    }

    public function start()
    {
        new IoServer(
            new HttpServer(
                new WsServer(
                    new WebSocketComponent($this->store)
                )
            ),
            new Server('127.0.0.1:1338', $this->loop),
            $this->loop
        );
    }
}