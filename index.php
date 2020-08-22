#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use Mailamie\Config;
use Mailamie\StartServer;
use Symfony\Component\Console\Application;

$application = new Application();

$localConfig = @include getenv("HOME") . '/.mailamie.config.php';

$config = new Config(require 'config.php', $localConfig ?: null);

$command = new StartServer($config);

$application->add($command);
$application->setDefaultCommand($command->getName(), true);

$application->run();
