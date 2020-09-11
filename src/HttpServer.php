<?php declare(strict_types=1);

namespace Mailamie;

use Mailamie\Emails\Store;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Http\Server;
use React\Socket\Server as SocketServer;

class HttpServer
{
    private LoopInterface $loop;
    private Emails\Store $messageStore;
    private string $host;
    private string $websocketHost;

    public function __construct(string $host, string $websocketHost, LoopInterface $loop, Store $messageStore)
    {
        $this->loop = $loop;
        $this->messageStore = $messageStore;
        $this->host = $host;
        $this->websocketHost = $websocketHost;
    }

    public function start(): void
    {
        $server = new Server(
            $this->loop,
            fn (ServerRequestInterface $request) => (
                new HttpController($this->messageStore, $this->websocketHost)
            )
            ->route($request)
        );

        $socket = new SocketServer($this->host, $this->loop);
        $server->listen($socket);
    }
}
