<?php

namespace Tests\Unit\Emails;

use Mailamie\Config;
use Mailamie\Emails\Message;
use Mailamie\Emails\Store;
use PHPUnit\Framework\TestCase;
use Tests\Traits\Messages;

class StoreTest extends TestCase
{
    use Messages;

    /** @test */
    public function it_allows_to_store_and_search_messages()
    {
        $message = $this->createMessage();

        $store = new Store();

        $store->store($message);

        $this->assertEquals($message, $store->get($message->getId()));
    }

    /** @test */
    public function it_allows_to_register_callbacks_for_new_messages()
    {
        $message = $this->createMessage();

        $store = new Store();

        $store->onNewMessage(function (Message $message) use (&$result) {
            $result = $message;
        });

        $store->store($message);

        $this->assertEquals($result, $message);
    }

    /** @test */
    public function it_allows_to_retrieve_all_messages()
    {
        $message1 = $this->createMessage();
        $message2 = $this->createMessage();

        $store = new Store();

        $store->store($message1);
        $store->store($message2);

        $this->assertEquals([
            [
                "id"         => $message1->getId(),
                "from"       => $message1->getSender(),
                "recipients" => $message1->getRecipients(),
                "subject"    => $message1->getSubject(),
                "created_at" => $message1->getCreatedAt()->format(Config::DATE_FORMAT),
            ],
            [
                "id"         => $message2->getId(),
                "from"       => $message2->getSender(),
                "recipients" => $message2->getRecipients(),
                "subject"    => $message2->getSubject(),
                "created_at" => $message2->getCreatedAt()->format(Config::DATE_FORMAT),
            ]
        ], $store->all());
    }
}
