<?php

namespace Mailamie;

use Exception;
use Mailamie\Emails\Parser;
use Mailamie\Events\DebugEvent;
use Mailamie\Events\Message;
use Mailamie\Events\Request;
use Mailamie\Events\Response;
use Mailamie\Events\ServerStarted;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class StartServer extends Command
{
    protected static $defaultName = 'start-server';
    protected ?OutputInterface $output;

    protected function configure()
    {
        $this
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('host', 'H', InputOption::VALUE_REQUIRED)
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        /** @var string $host */
        $host = $input->getOption('host') ?: '127.0.0.1:8025';

        $section = $output->section();
        $section->writeln("\n<comment>Starting server...</comment>\n");

        sleep(1);

        $dispatcher = new EventDispatcher();

        $server = new Server($host, $dispatcher);

        $dispatcher->addListener(ServerStarted::class, function (ServerStarted $event) use ($section) {
            $formatter = $this->getHelper('formatter');
            $message = ['✓ SERVER UP AND RUNNING', "Listening on {$event->host}"];
            $formattedBlock = $formatter->formatBlock($message, 'info', true);
            $section->overwrite($formattedBlock);
        });

        $dispatcher->addListener(Message::class, function (Message $message) {
            $this->handleMessage($message);
        });

        if ($output->isVerbose()) {
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

        if ($output->isVeryVerbose()) {
            $dispatcher->addListener(DebugEvent::class, function (DebugEvent $event) {
                $this->writeFormatted('DEBUG',  $event->param, 'yellow');
            });
        }

        $server->start();

        return Command::SUCCESS;
    }

    private function handleMessage(Message $message): void
    {
        $parser = new Parser();

        $email = $parser->parse($message->body);

        $this->writeFormatted(
            'MESSAGE',
            "<options=bold>New message for:</> " . implode("; ", $email->recipients),
            'green'
        );

        $this->writeTable([
            ['From', $email->sender],
            ['To', implode("; ", $email->recipients)],
            ['Cc', implode("; ", $email->recipients)],
            ['Bcc', implode("; ", $email->recipients)],
            ['Subject', $email->subject],
            ['Excerpt', 'Lorem ipsum dolet lorym ...'],
            ['Preview', '<href=https://symfony.com>https://symfony.com</>']
        ]);
    }

    private function getOutput(): OutputInterface
    {
        if (!$this->output) {
            throw new Exception('Output should be defined by now');
        }
        return $this->output;
    }

    /**
     * @param array[] $rows
     * @return void
     */
    private function writeTable(array $rows): void
    {
        $table = new Table($this->getOutput());
        $table->setRows($rows);
        $table->setColumnMaxWidth(1, 60);
        $table->render();
    }

    private function writeFormatted(string $section, string $content, string $color): void
    {
        $output = $this->getOutput();

        if (!$output->isDebug()) {
            $content = mb_strimwidth($content, 0, 80, '...');
        }

        $formattedLine = $this
            ->getHelper('formatter')
            ->formatSection(
                $section,
                $content,
                "fg={$color}"
            );

        $output->writeln($formattedLine);
    }
}
