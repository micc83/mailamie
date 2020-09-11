<?php declare(strict_types=1);

namespace Mailamie;

use Exception;
use Mailamie\Console\Helpers;
use Mailamie\Emails\Parser;
use Mailamie\Emails\Store as MessageStore;
use Mailamie\Events\DebugEvent;
use Mailamie\Events\Message;
use Mailamie\Events\Request;
use Mailamie\Events\Response;
use Mailamie\Events\ServerStarted;
use React\EventLoop\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class StartServer extends Command
{
    use Helpers;

    protected static $defaultName = 'mailamie';
    private Config $config;

    const DATE_FORMAT = "";

    public function __construct(Config $config)
    {
        parent::__construct();
        $this->config = $config;
    }

    protected function configure(): void
    {
        $this->setDescription('Mailamie is catch all SMTP server for testing.');
        $this->setHelp(
            "You can define custom configuration from the file ~/.mailamie.config.php,\n" .
            "check the project readme file at https://github.com/micc83/mailamie\n" .
            "for all the available settings."
        );
        $this->addUsage('--host=127.0.0.1 --port=25    Ex. SMTP Host definition');
        $this->setDefinition(
            new InputDefinition([
                new InputOption(
                    'host',
                    'H',
                    InputOption::VALUE_REQUIRED,
                    'Set the host on which to listen for calls'
                ),
                new InputOption(
                    'port',
                    'p',
                    InputOption::VALUE_REQUIRED,
                    'Set the port on which to listen for SMTP calls'
                )
            ])
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new Exception('Expected instance of ConsoleOutputInterface');
        }

        $this->output = $output;
        $this->input = $input;

        $dispatcher = new EventDispatcher();

        $loop = Factory::create();

        $smtpServer = new SmtpServer($this->getSmtpHost(), $loop, $dispatcher);

        $messageStore = new MessageStore();

        $webServer = new HttpServer(
            $this->getHttpHost(),
            $this->getWebsocketHost(),
            $loop,
            $messageStore
        );

        $websocketServer = new WebSocketServer(
            $this->getWebsocketHost(),
            $loop,
            $messageStore
        );

        $this->registerEventListenersOn($dispatcher, $messageStore);

        $smtpServer->start();
        $webServer->start();
        $websocketServer->start();

        $loop->run();

        return Command::SUCCESS;
    }

    private function registerEventListenersOn(EventDispatcher $dispatcher, MessageStore $messageStore): void
    {
        $startingSection = $this->startingBanner();

        $dispatcher->addListener(ServerStarted::class, function (ServerStarted $event) use ($startingSection) {
            $this->writeInfoBlockOn(
                $startingSection,
                '✓ SERVER UP AND RUNNING',
                "SMTP listening on {$event->host}\n  Web interface at http://{$this->getHttpHost()}"
            );
        });

        $dispatcher->addListener(Message::class, function (Message $message) use ($messageStore) {
            $this->handleMessage($message, $messageStore);
        });

        if ($this->getOutput()->isVerbose()) {
            $this->addVerboseListeners($dispatcher);
        }

        if ($this->getOutput()->isVeryVerbose()) {
            $this->addVeryVerboseListeners($dispatcher);
        }
    }

    private function handleMessage(Message $message, MessageStore $messageStore): void
    {
        $parser = new Parser();

        $email = $parser->parse($message->body, $message->recipients);
        $messageStore->store($email);

        $this->writeFormatted(
            'MESSAGE',
            "<options=bold>Hurray, you got a new message!</>",
            'green'
        );

        $this->writeTable($email->toTable());
    }

    private function getSmtpHost(): string
    {
        $host = $this->getInput()->getOption('host') ?:$this->config->get('smtp.host');
        $port = $this->getInput()->getOption('port') ?:$this->config->get('smtp.port');

        return "{$host}:{$port}";
    }

    private function getHttpHost(): string
    {
        $host = $this->getInput()->getOption('host') ?:$this->config->get('http.host');
        $port = $this->config->get('http.port');

        return "{$host}:{$port}";
    }

    private function getWebsocketHost(): string
    {
        $host = $this->getInput()->getOption('host') ?:$this->config->get('websocket.host');
        $port = $this->config->get('websocket.port');

        return "{$host}:{$port}";
    }

    private function startingBanner(): ConsoleSectionOutput
    {
        $section = $this->createSection();

        $section->writeln("\n<comment>Starting server...</comment>\n");

        return $section;
    }

    private function addVerboseListeners(EventDispatcher $dispatcher): void
    {
        $dispatcher->addListener(Request::class, function (Request $request) {
            $this->writeFormatted('CLIENT', $request->body, 'cyan');
        });

        $dispatcher->addListener(Response::class, function (Response $response) {
            $this->writeFormatted(
                'SERVER',
                "<options=bold>{$response->code}</> - {$response->body}",
                'magenta'
            );
        });
    }

    private function addVeryVerboseListeners(EventDispatcher $dispatcher): void
    {
        $dispatcher->addListener(DebugEvent::class, function (DebugEvent $event) {
            $this->writeFormatted('DEBUG', $event->param, 'yellow');
        });
    }
}
