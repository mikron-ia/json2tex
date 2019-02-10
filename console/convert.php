#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new \Mikron\json2tex\Infrastructure\ConvertTreeCommand());
$application->add(new \Mikron\json2tex\Infrastructure\ConvertTraitCommand());
$application->add(new \Mikron\json2tex\Infrastructure\ConvertLegacyCommand());
$application->run();
