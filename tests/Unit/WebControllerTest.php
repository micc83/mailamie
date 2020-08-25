<?php declare(strict_types=1);

namespace Tests\Unit;

use Mailamie\Emails\Store;
use Mailamie\WebController;
use PHPUnit\Framework\TestCase;
use Tests\Traits\Messages;

class WebControllerTest extends TestCase
{
    use Messages;

    /** @test */
    public function a_websocket_message_is_sent_to_connect_clients_on_new_email(): void
    {
        $store = new Store();
        $controller = new WebController($store);


    }
}
