<?php

namespace Mailamie\Console;

use Exception;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait Helpers
 * @package Mailamie\Console
 * @mixin
 */
trait Helpers
{
    protected ?OutputInterface $output;
    protected ?InputInterface $input;

    private function getOutput(): OutputInterface
    {
        return $this->output;
    }

    private function getInput(): InputInterface
    {
        return $this->input;
    }

    private function createSection(): ConsoleSectionOutput
    {
        return $this->getOutput()->section();
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

    private function writeInfoBlockOn(ConsoleSectionOutput $section, string $title, string $subtitle)
    {
        $formatter = $this->getHelper('formatter');
        $message = [$title, $subtitle];
        $formattedBlock = $formatter->formatBlock($message, 'info', true);
        $section->overwrite($formattedBlock);
    }

    /**
     * @param string $section
     * @param string $content
     * @param string $color
     */
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