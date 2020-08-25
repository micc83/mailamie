<?php

declare(strict_types=1);

namespace Tests\Unit;

use Mailamie\Events\Event;
use Mailamie\Events\Message;
use Mailamie\Events\Request;
use Mailamie\Events\Response;
use Mailamie\SmtpConnection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use React\Socket\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class SmtpConnectionTest
 * @package Tests
 */
class SmtpConnectionTest extends TestCase
{
    /** @test */
    public function on_ready_send_220_service_ready()
    {
        $smtp = new SmtpConnection(...[$connection, $dispatcher] = $this->createMocks());

        $this->expectWrite($connection, 220);

        $this->expectDispatch(
            $dispatcher,
            new Response(220, "Service ready")
        );

        $smtp->ready();
    }

    /** @test */
    public function it_allows_to_handle_EHLO_handshake()
    {
        $smtp = new SmtpConnection(...[$connection, $dispatcher] = $this->createMocks());

        $this->expectWrite($connection, 250);
        $this->expectDispatch(
            $dispatcher,
            new Request("EHLO localhost\r\n"),
            new Response(250, "OK")
        );

        $smtp->handle("EHLO localhost\r\n");
    }

    /** @test */
    public function it_allows_to_handle_RCPT_TO_commands()
    {
        $smtp = new SmtpConnection(...[$connection, $dispatcher] = $this->createMocks());

        $this->expectWrite($connection, 250);
        $this->expectDispatch(
            $dispatcher,
            new Request("RCPT TO:<micc83@gmail.com>\r\n"),
            new Response(250, "OK")
        );

        $smtp->handle("RCPT TO:<micc83@gmail.com>\r\n");
    }

    /** @test */
    public function it_allows_to_handle_DATA_commands()
    {
        $smtp = new SmtpConnection(...[$connection, $dispatcher] = $this->createMocks());

        $this->expectWrite($connection, 354);
        $this->expectDispatch(
            $dispatcher,
            new Request("DATA\r\n"),
            new Response(354, "Start mail input; end with <CRLF>.<CRLF>")
        );

        $smtp->handle("DATA\r\n");
    }

    /** @test */
    public function it_allows_to_capture_a_new_message()
    {
        $smtp = new SmtpConnection(...[$connection, $dispatcher] = $this->createMocks());

        $this->expectWrite($connection, 250, 354, 250);
        $this->expectDispatch(
            $dispatcher,
            new Request("RCPT TO:<micc83@gmail.com>\r\n"),
            new Response(250, "OK"),
            new Request("DATA\r\n"),
            new Response(354, "Start mail input; end with <CRLF>.<CRLF>"),
            new Request("My message content..."),
            new Request("Final row\r\n.\r\n"),
            new Response(250, "OK"),
            new Message("My message content...Final row\r\n.\r\n", ["micc83@gmail.com"])
        );

        $smtp->handle("RCPT TO:<micc83@gmail.com>\r\n");
        $smtp->handle("DATA\r\n");
        $smtp->handle("My message content...");
        $smtp->handle("Final row\r\n.\r\n");
    }

    /** @test */
    public function it_allows_to_handle_QUIT_commands()
    {
        $smtp = new SmtpConnection(...[$connection, $dispatcher] = $this->createMocks());

        $this->expectWrite($connection, 221);
        $this->expectDispatch(
            $dispatcher,
            new Request("QUIT\r\n"),
            new Response(221, "Service closing transmission channel")
        );

        $smtp->handle("QUIT\r\n");
    }

    /**
     * @return array{0: ConnectionInterface|MockObject, 1?: EventDispatcher|MockObject}
     */
    private function createMocks(): array
    {
        return [
            $this->createMock(ConnectionInterface::class),
            $this->createMock(EventDispatcher::class)
        ];
    }

    private function expectDispatch(MockObject $dispatcher, Event ...$responses): void
    {
        $dispatcher
            ->expects(self::exactly(count($responses)))
            ->method('dispatch')
            ->withConsecutive(... array_map(function ($response) {
                return [$response];
            }, $responses));
    }

    private function expectWrite(MockObject $connection, int ...$codes): void
    {
        $connection
            ->expects(self::exactly(count($codes)))
            ->method('write')
            ->withConsecutive(... array_map(function (int $code) {
                return ["{$code}\r\n"];
            }, $codes));
    }
}
