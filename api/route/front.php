<?php

$app->get('/', function (Silex\Application $app) {
    return "This is basic front page. Please choose a functionality you wish to access from 'content' area";
});

$app->get('/tex/', function (Silex\Application $app) {
    $document = new \Mikron\json2tex\Domain\Entity\Document('[]');
    return $document->getDocument();
});
