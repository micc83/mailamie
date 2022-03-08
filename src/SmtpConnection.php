<?php declare(strict_types=1);

namespace Mailamie;

use Mailamie\Events\Message;
use Mailamie\Events\Request;
use Mailamie\Events\Response;
use Psr\EventDispatcher\EventDispatcherInterface;
use React\Socket\ConnectionInterface;

class SmtpConnection
{
    private ConnectionInterface $connection;
    private EventDispatcherInterface $events;
    private string $messageBody = '';
    private bool $collectingData = false;

    /** @var string[] */
    private array $recipients = [];

    /**
     * Response codes
     */
    const READY = 220;
    const OK = 250;
    const CLOSING = 221;
    const START_MAIL_INPUT = 354;

    /**
     * Response code descriptions
     * @var array<int,string>
     */
    private static array $statusDescriptions = [
        self::READY            => 'Service ready',
        self::OK               => 'OK',
        self::CLOSING          => 'Service closing transmission channel',
        self::START_MAIL_INPUT => 'Start mail input; end with <CRLF>.<CRLF>'
    ];

    public function __construct(
        ConnectionInterface $connection,
        EventDispatcherInterface $events
    ) {
        $this->connection = $connection;
        $this->events = $events;
    }

    public function ready(): void
    {
        $this->collectingData = false;
        $this->send(static::READY, 'mailamie');
    }

    public function handle(string $data): void
    {
        $this->events->dispatch(new Request($data));

        if (preg_match("/^(EHLO|HELO|MAIL FROM:)/", $data)) {
            $this->send(static::OK);
        } elseif (preg_match("/^RCPT TO:<(.*)>/", $data, $matches)) {
            $this->addRecipient($matches[0]);
            $this->send(static::OK);
        } elseif ($data === "QUIT\r\n") {
            $this->send(static::CLOSING);
        } elseif ($data === "DATA\r\n") {
            $this->collectingData = true;
            $this->send(static::START_MAIL_INPUT);
        } elseif ($this->collectingData) {
            if ($this->endOfContentDetected($data)) {
                $this->addToMessageBody($data);
                $this->send(static::OK);
                $this->dispatchMessage();
                $this->collectingData = false;
            } else {
                $this->addToMessageBody($data);
            }
        }
    }

    private function endOfContentDetected(string $data): bool
    {
        return (bool)preg_match("/\r\n\.\r\n$/", $data);
    }

    private function dispatchMessage(): void
    {
        $this->events->dispatch(
            new Message($this->messageBody, $this->recipients)
        );
    }

    private function addRecipient(string $recipient): void
    {
        $this->recipients[] = preg_replace('/^RCPT TO:<(.*)>/', '$1', $recipient);
    }

    private function addToMessageBody(string $content): void
    {
        /**
         * Remove double dots from rows start
         * @see https://github.com/micc83/mailamie/issues/13
         */
        $content = preg_replace("/^(\.\.)/m", ".", $content);

        $this->messageBody .= $content;
    }

    private function send(int $statusCode, string $comment = null): void
    {
        $this->events->dispatch(
            new Response($statusCode, self::$statusDescriptions[$statusCode])
        );
        $response = implode(" ", array_filter([$statusCode, $comment]));
        $this->connection->write("{$response} \r\n");
    }
}
