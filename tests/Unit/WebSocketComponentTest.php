<?php declare(strict_types=1);

namespace Tests\Unit;

use Mailamie\Emails\Message;
use Mailamie\Emails\Store;
use Mailamie\WebSocketComponent;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;
use Tests\Traits\Messages;

class WebSocketComponentTest extends TestCase
{
    use Messages;

    /** @test */
    public function a_websocket_message_is_sent_to_connect_clients_on_new_email(): void
    {
        $store = new Store();
        $websocket = new WebSocketComponent($store);
        $message = $this->createMessage();

        $connection = $this->createMock(ConnectionInterface::class);

        $connection->expects(self::once())->method('send');

        $websocket->onOpen($connection);
        $store->store($message);

        $websocket->onClose($connection);
        $store->store($message);
    }
}
