<?php

namespace Mailamie\Emails;

use Exception;

class Store
{
    /**
     * @var array<string,Message>
     */
    private array $messages = [];

    public function store(Message $message): string
    {
        $id = (string)uniqid();
        $this->messages[$id] = $message;

        return $id;
    }

    public function get(string $id): Message
    {
        if (!isset($this->messages[$id])) {
            throw new Exception("Cannot find any message with the given ID");
        }

        return $this->messages[$id];
    }

    /**
     * @return array[]
     */
    public function all(): array
    {
        return array_values(array_map(function (Message $message) {
            return [
                'id'         => $message->id,
                'from'       => $message->sender,
                'recipients' => $message->recipients,
                'subject'    => $message->subject
            ];
        }, $this->messages));
    }
}