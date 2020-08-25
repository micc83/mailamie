<?php declare(strict_types=1);

namespace Mailamie;

use Mailamie\Emails\Store;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Http\Server;
use React\Socket\Server as SocketServer;

class WebServer
{
    private LoopInterface $loop;
    private Emails\Store $messageStore;
    private string $host;

    public function __construct(string $host, LoopInterface $loop, Store $messageStore)
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
