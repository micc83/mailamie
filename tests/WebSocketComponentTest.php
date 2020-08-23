<?php

namespace Tests;

use Mailamie\Emails\Message;
use Mailamie\Emails\Store;
use Mailamie\WebSocketComponent;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;

class WebSocketComponentTest extends TestCase
{
    /** @test */
    public function a_websocket_message_is_sent_to_connect_clients_on_new_email()
    {
        $store = new Store();
        $websocket = new WebSocketComponent($store);
        $message = new Message('','',[],[],'','','');

        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects(self::once())->method('send');

        $websocket->onOpen($connection);
        $store->store($message);

        $websocket->onClose($connection);
        $store->store($message);
    }
}