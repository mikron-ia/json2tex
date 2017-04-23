<?php

$configPath = __DIR__ . '/../config/';

$configMain = require($configPath . 'main.php');

$app['config'] = $configMain;
