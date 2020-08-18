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

    public function __construct(StreamSelectLoop $loop, Store $messageStore)
    {
        $this->loop = $loop;
        $this->messageStore = $messageStore;
    }

    public function start(): void
    {
        $server = new Server($this->loop, function (ServerRequestInterface $request) {
            $path = $request->getUri()->getPath();

            if (preg_match('/^\/api\//i', $path)) {
                try {
                    if (preg_match('/^\/api\/messages\/?$/i', $path)) {
                        return $this->json($this->messageStore->all());
                    }

                    if (preg_match('/^\/api\/messages\/(.*)$/i', $path, $matches)) {
                        $id = (string)$matches[1];
                        $message = $this->messageStore->get($id);
                        return $this->json($message->toArray());
                    }
                } catch (Throwable $e) {
                    return new Response(
                        500, ['Content-Type' => 'text/html'],
                        "<h2>{$e->getMessage()}</h2> <pre>{$e->getTraceAsString()}</pre>"
                    );
                }
            }

            $path = $path === '/' ? '/index.htm' : $path;

            if (file_exists('public' . $path)) {
                return new Response(
                    200, ['Content-Type' => 'text/html'],
                    file_get_contents('public' . $path)
                );
            }

            return new Response(
                404, ['Content-Type' => 'text/html'],
                "<h2>404 - Page or content not found</h2>"
            );
        });

        $socket = new SocketServer(8080, $this->loop);
        $server->listen($socket);
    }

    /**
     * @param array|object $data
     * @return Response
     */
    private function json($data): Response
    {
        return new Response(
            200, ['Content-Type' => 'application/json'],
            json_encode($data)
        );
    }
}