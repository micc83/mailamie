<?php declare(strict_types=1);

namespace Tests\Integration;

use Exception;
use Mailamie\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\Message;
use React\ChildProcess\Process;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Browser;
use React\Promise\ExtendedPromiseInterface;
use RingCentral\Psr7\Response;

use function Ratchet\Client\connect;
use function React\Promise\all;

class IntegrationTest extends TestCase
{
    private Process $process;
    private LoopInterface $loop;
    private ?int $expectedSteps;
    private int $step;
    private ?ExpectationFailedException $asyncException = null;
    private ?WebSocket $websocket = null;
    private Config $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new Config(require('config.php'));

        $this->expectedSteps = null;
        $this->step = 0;

        $this->loop = Factory::create();

        $this->process = new Process('exec php index.php');
        $this->process->start($this->loop);

        $this->loop->addTimer(2, function () {
            $this->process->terminate();
            $message = 'Process should have terminated within 2 seconds.';
            throw new ExpectationFailedException($message);
        });
    }

    protected function tearDown(): void
    {
        $this->process->terminate();
        if (!is_null($this->expectedSteps)) {
            $this->assertEquals(
                $this->expectedSteps,
                $this->step,
                "Not all {$this->expectedSteps} steps have been completed"
            );
        }
        parent::tearDown();
    }

    /** @test */
    public function should_start_and_listen_for_smtp_calls(): void
    {
        $this->expectedSteps = 3;

        $this->process->stdout->on('data', function ($chunk) {
            $this->step++;

            // Starting
            if ($this->step === 1) {
                $this->assertServerStarting($chunk);
            }

            // Started
            if ($this->step === 2) {
                $this->assertServerUpAndRunning($chunk);
                $this
                    ->connectToWebsocket()
                    ->done(function () {
                        $this->sendMail();
                    });
            }

            // Email sent
            if ($this->step === 3) {
                $this->assertMessageReceived($chunk);
                $this->assertWebsocketWorking();
                $this
                    ->assertWebBrowserWorking()
                    ->always(function () {
                        $this->loop->stop();
                    });
            }
        });

        $this->endLoop();
    }

    private function endLoop(): void
    {
        $this->loop->run();

        $this->websocket->close();

        if ($this->asyncException) {
            throw $this->asyncException;
        }
    }

    private function assertWebsocketWorking(): void
    {
        $this->websocket->on('message', function (Message $msg) use (&$messageCount) {
            $this->assertStringContainsString('"from":"Mailer <from@example.com>"', (string)$msg);
            $messageCount++;
        });

        $this->websocket->on('close', function () use (&$messageCount) {
            $this->assertEquals(1, $messageCount);
        });
    }

    private function connectToWebsocket(): ExtendedPromiseInterface
    {
        /** @var ExtendedPromiseInterface $promise */
        $promise = connect("ws://{$this->config->get('websocket.host')}", [], [], $this->loop)
            ->then(function (WebSocket $conn) {
                $this->websocket = $conn;
            }, function (Exception $e) {
                $this->storeAsyncException($e);
            });

        return $promise;
    }

    private function assertServerStarting(string $chunk): void
    {
        $this->assertStringContainsString('Starting server...', $chunk);
    }

    private function assertServerUpAndRunning(string $chunk): void
    {
        $this->assertStringContainsString('SERVER UP AND RUNNING', $chunk);
        $this->assertStringContainsString("SMTP listening on tcp://{$this->config->get('smtp.host')}", $chunk);
        $this->assertStringContainsString("Web interface at http://{$this->config->get('web.host')}", $chunk);
    }

    private function assertMessageReceived(string $chunk): void
    {
        $this->assertStringContainsString('Hurray, you got a new message!', $chunk);
        $this->assertStringContainsString('Mailer <from@example.com>', $chunk);
        $this->assertStringContainsString('Joe User <joe@example.net>; ellen@example.com', $chunk);
        $this->assertStringContainsString('Here is the subject, welcome to New york!', $chunk);
    }

    private function assertWebBrowserWorking(): ExtendedPromiseInterface
    {
        $client = new Browser($this->loop);

        $request1 = $client
            ->withTimeout(1)
            ->get("http://{$this->config->get('web.host')}/api/messages")
            ->then(function (Response $response) {
                $content = $response->getBody()->getContents();
                $this->assertStringContainsString('"from":"Mailer <from@example.com>"', $content);
            }, function (Exception $e) {
                $this->storeAsyncException($e);
            });

        $request2 = $client
            ->withTimeout(1)
            ->get("http://{$this->config->get('web.host')}")
            ->then(function (Response $response) {
                $content = $response->getBody()->getContents();
                $this->assertStringContainsString('<div id="app">', $content);
            }, function (Exception $e) {
                $this->storeAsyncException($e);
            });

        /** @var ExtendedPromiseInterface $promise */
        $promise = all([$request1, $request2]);

        return $promise;
    }

    private function storeAsyncException(Exception $e): void
    {
        $this->asyncException = new ExpectationFailedException($e->getMessage(), null, $e);
    }

    private function sendMail(): bool
    {
        $mail = new PHPMailer();

        $mail->isSMTP();

        [$mail->Host, $mail->Port] = explode(':', $this->config->get('smtp.host'));
        $mail->Timeout = 1;

        $mail->setFrom('from@example.com', 'Mailer');
        $mail->addAddress('joe@example.net', 'Joe User');
        $mail->addAddress('ellen@example.com');
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');

        $mail->Subject = 'Here is the subject, welcome to New york!';
        $mail->Body = 'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.";

        return $mail->send();
    }
}
