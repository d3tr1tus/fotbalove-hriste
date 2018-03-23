<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->setDebugMode('2a02:908:3031:1900:bc96:f5e4:7e1a:5e8c'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
        ->addDirectory(__DIR__)
        ->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
if ($_SERVER['SERVER_NAME'] == 'fotbalhriste.cz' || $_SERVER['SERVER_NAME'] == 'www.fotbalhriste.cz') {
    $configurator->addConfig(__DIR__ . '/config/config.production.neon');
} else {
    $configurator->addConfig(__DIR__ . '/config/config.local.neon');
}

$container = $configurator->createContainer();

return $container;
