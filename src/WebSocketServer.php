<?php declare(strict_types=1);

namespace Mailamie;

use Mailamie\Emails\Store;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\LoopInterface;
use React\Socket\Server;

class WebSocketServer
{
    private LoopInterface $loop;
    private Store $store;
    private string $host;

    public function __construct(string $host, LoopInterface $loop, Store $store)
    {
        $this->loop = $loop;
        $this->store = $store;
        $this->host = $host;
    }

    public function start(): void
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
