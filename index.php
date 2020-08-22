#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Mailamie\StartServer;
use Symfony\Component\Console\Application;

$application = new Application();

$command = new StartServer();

$application->add($command);
$application->setDefaultCommand($command->getName(), true);

$application->run();
