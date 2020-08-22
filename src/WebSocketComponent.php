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
    /**
     * @var SplObjectStorage
     */
    protected SplObjectStorage $clients;
    private Store $store;

    public function __construct(Store $store)
    {
        $this->clients = new SplObjectStorage;
        $this->store = $store;
        $this->noticeNewMessagesToClients();
    }

    private function noticeNewMessagesToClients(): void
    {
        $this->store->onNewMessage(function (Message $message) {
            foreach ($this->clients as $client) {
                $client->send(json_encode($message->toArray()));
            }
        });
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // ..
    }
}
