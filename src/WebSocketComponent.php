<?php

namespace Mailamie;

use Exception;
use Mailamie\Emails\Message;
use Mailamie\Emails\Store;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class WebSocketComponent implements MessageComponentInterface
{
    protected SplObjectStorage $clients;
    private Store $store;

    public function __construct(Store $store)
    {
        $this->clients = new SplObjectStorage;
        $this->store = $store;
    }

    function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        $this->store->onNewMessage(function (Message $message) use ($conn) {
            $conn->send(json_encode($message->toArray()));
        });
    }

    function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    function onMessage(ConnectionInterface $from, $msg)
    {
        // ..
    }
}