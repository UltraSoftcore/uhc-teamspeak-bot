#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use Eluinhost\TSChannelRemover\ChannelRemoveCommand;
use Eluinhost\TSChannelRemover\ChannelRemoverApplication;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
$loader->load('config.yml');

$application = new Application('uhc-teamspeak-bot');

$application->addCommands([
   new ChannelRemoveCommand($container)
]);

$application->run();