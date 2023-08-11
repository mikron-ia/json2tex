#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Mikron\json2tex\Infrastructure\ConvertLegacyCommand;
use Mikron\json2tex\Infrastructure\ConvertTraitCommand;
use Mikron\json2tex\Infrastructure\ConvertTreeCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new ConvertTreeCommand());
$application->add(new ConvertTraitCommand());
$application->add(new ConvertLegacyCommand());

$application->run();
