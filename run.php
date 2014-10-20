#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use Eluinhost\TSChannelRemover\ListChannelsCommand;
use Eluinhost\TSChannelRemover\RemoveIdleChannelsCommand;
use Eluinhost\TSChannelRemover\ShuffleChannelsCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
$loader->load('config.yml');

$application = new Application('uhc-teamspeak-bot');

$application->addCommands([
   new RemoveIdleChannelsCommand(
       $container->get('teamspeak_server'),
       $container->getParameter('teamspeak.channelID'),
       $container->getParameter('teamspeak.excludes'),
       $container->getParameter('teamspeak.allowedMins')
   ),
    new ShuffleChannelsCommand(
        $container->get('teamspeak_server'),
        $container->getParameter('teamspeak.channelID'),
        $container->getParameter('teamspeak.excludes')
    ),
    new ListChannelsCommand(
        $container->get('teamspeak_server')
    )
]);

$application->run();