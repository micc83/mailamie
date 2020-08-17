<?php

namespace Mailamie;

use Exception;
use Mailamie\Console\Helpers;
use Mailamie\Emails\Parser;
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
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class StartServer extends Command
{
    use Helpers;

    protected static $defaultName = 'start-server';

    protected function configure()
    {
        $this->setDefinition(
            new InputDefinition([
                new InputOption('host', 'H', InputOption::VALUE_REQUIRED)
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
        $this->output = $output;
        $this->input = $input;

        $dispatcher = new EventDispatcher();

        $loop = Factory::create();

        $smtp = (new SmtpServer($this->getHost(), $loop, $dispatcher));

        $this->registerEventListenersOn($dispatcher);

        $smtp->start();

        $loop->run();

        return Command::SUCCESS;
    }

    private function registerEventListenersOn(EventDispatcher $dispatcher)
    {
        $startingSection = $this->startingBanner();

        $dispatcher->addListener(ServerStarted::class, function (ServerStarted $event) use ($startingSection) {
            $this->writeInfoBlockOn(
                $startingSection,
                '✓ SERVER UP AND RUNNING',
                "Listening on {$event->host}"
            );
        });

        $dispatcher->addListener(Message::class, function (Message $message) {
            $this->handleMessage($message);
        });

        if ($this->getOutput()->isVerbose()) {
            $this->addVerboseListeners($dispatcher);
        }

        if ($this->getOutput()->isVeryVerbose()) {
            $this->addVeryVerboseListeners($dispatcher);
        }
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

    private function getHost(): string
    {
        return (string)$this->getInput()->getOption('host');
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
