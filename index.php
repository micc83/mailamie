#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use Mailamie\Config;
use Mailamie\StartServer;
use Symfony\Component\Console\Application;

$localConfig = @include getenv("HOME") . '/.mailamie.config.php';

$config = new Config(require 'config.php', $localConfig ?: null);

$application = new Application('mailamie', Config::VERSION);

$command = new StartServer($config);

$application->add($command);
$application->setDefaultCommand($command->getName(), true);

$application->run();
