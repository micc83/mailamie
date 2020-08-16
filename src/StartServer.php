<?php

namespace Mailamie;

use Mailamie\Emails\Parser;
use Mailamie\Events\DebugEvent;
use Mailamie\Events\Message;
use Mailamie\Events\Request;
use Mailamie\Events\Response;
use Mailamie\Events\ServerStarted;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;

class StartServer extends Command
{
    protected static $defaultName = 'start-server';

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
        /** @var string $host */
        $host =  $input->getOption('host') ?: '127.0.0.1:8025';

        $section = $output->section();
        $section->writeln("<comment>Starting server...</comment>");

        sleep(1);

        $dispatcher = new EventDispatcher();

        $server = new Server($host, $dispatcher);

        $dispatcher->addListener(ServerStarted::class, function (ServerStarted $event) use ($section) {
            $formatter = $this->getHelper('formatter');
            $message = ['✓ SERVER UP AND RUNNING', "Listening on {$event->host}"];
            $formattedBlock = $formatter->formatBlock($message, 'info', true);
            $section->overwrite($formattedBlock);
        });

        $dispatcher->addListener(Message::class, function (Message $message) use ($output, $input) {


            $formatter = $this->getHelper('formatter');

            $parser = new Parser();

            $email = $parser->parse($message->body);

            $formattedLine = $formatter->formatSection(
                'MESSAGE',
                "<options=bold>New message for:</> " . implode("; ", $email->recipients),
                'fg=green'
            );
            $output->writeln($formattedLine);

            $table = new Table($output);
            $table->setRows(
                [
                    ['From', $email->sender],
                    ['To', implode("; ", $email->recipients)],
                    ['Preview', '<href=https://symfony.com>https://symfony.com</>']
                ]
            );
            $table->setColumnMaxWidth(1, 60);
            $table->render();
        });

        $dispatcher->addListener(DebugEvent::class, function (DebugEvent $event) use ($output) {
            $formatter = $this->getHelper('formatter');
            $formattedLine = $formatter->formatSection(
                'DEBUG ',
                $event->param,
                'fg=yellow'
            );
            $output->writeln($formattedLine);
        });

        $dispatcher->addListener(Request::class, function (Request $request) use ($output) {
            $formatter = $this->getHelper('formatter');
            $formattedLine = $formatter->formatSection(
                'CLIENT',
                $request->body,
                'fg=cyan'
            );
            $output->writeln($formattedLine);
        });

        $dispatcher->addListener(Response::class, function (Response $response) use ($output) {
            $formatter = $this->getHelper('formatter');
            $formattedLine = $formatter->formatSection(
                'SERVER',
                $response->body,
                'fg=magenta'
            );
            $output->writeln($formattedLine);
        });

        $server->start();

        return Command::SUCCESS;
    }
}
