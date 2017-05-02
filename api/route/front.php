<?php

$app->get('/', function (Silex\Application $app) {
    return "This is basic front page. Please choose a functionality you wish to access from 'content' area";
});

$app->get('/tex/', function (Silex\Application $app) {
    $document = new \Mikron\json2tex\Domain\Entity\Document('[]');
    return $document->getDocument();
});


$app->get('/from-file/{file}/', function (Silex\Application $app, $file) {
    $json = file_get_contents(__DIR__ . '/../../data/' . $file . '.json');
    $document = new \Mikron\json2tex\Domain\Entity\Document($json);
    return $document->getContent();
});
