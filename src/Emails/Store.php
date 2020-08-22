<?php

namespace Mailamie\Emails;

use Closure;
use Exception;

class Store
{
    /**
     * @var array<string,Message>
     */
    private array $messages = [];

    /**
     * @var Closure[]
     */
    private array $callbacks = [];

    public function store(Message $message): string
    {
        $id = (string)uniqid();
        $message->setId($id);
        $this->messages[$id] = $message;

        foreach ($this->callbacks as $callback) {
            $callback($message);
        }

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
                'subject'    => $message->subject,
                'created_at' => $message->created_at->format('Y-m-d H:i:s')
            ];
        }, $this->messages));
    }

    public function onNewMessage(Closure $callback)
    {
        $this->callbacks[] = $callback;
    }
}