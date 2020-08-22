<?php

namespace Tests;

use Mailamie\Emails\Store;
use Mailamie\WebSocketComponent;
use PHPUnit\Framework\TestCase;

class WebSocketComponentTest extends TestCase
{
    /** @test */
    public function one_and_one_are_two()
    {
        $this->assertEquals(2, 1 + 1);

        $store = new Store();
        $websocket = new WebSocketComponent($store);


    }
}