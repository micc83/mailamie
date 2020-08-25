<?php declare(strict_types=1);

namespace Mailamie\Emails;

use Closure;
use Exception;
use Mailamie\Config;

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

    public function store(Message $message): void
    {
        $this->messages[$message->getId()] = $message;

        foreach ($this->callbacks as $callback) {
            $callback($message);
        }
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
                'id'         => $message->getId(),
                'from'       => $message->getSender(),
                'recipients' => $message->getRecipients(),
                'subject'    => $message->getSubject(),
                'created_at' => $message->getCreatedAt()->format(Config::DATE_FORMAT)
            ];
        }, $this->sortedByDate()));
    }

    /**
     * @return Message[]
     */
    private function sortedByDate(): array
    {
        $messages = $this->messages;

        usort($messages, function (Message $a, Message $b) {
            return $a->getCreatedAt()->getTimestamp() < $b->getCreatedAt()->getTimestamp() ? 1 : -1;
        });

        return $messages;
    }

    public function onNewMessage(Closure $callback): void
    {
        $this->callbacks[] = $callback;
    }
}
