<?php

$configPath = __DIR__ . '/../config/';

/* Level 0: main config */
$configMain = require($configPath . 'main.php');

/* Level 2: Specific story / epic; loaded first, as it determines choice of Level 1 config option */
if (file_exists($configPath . 'epic.php')) {
    $configEpic = require($configPath . 'epic.php');
} else {
    $configEpic = [];
}

$app['config'] = array_replace_recursive($configMain, $configEpic);
