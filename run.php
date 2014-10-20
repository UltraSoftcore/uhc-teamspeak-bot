#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use Eluinhost\TSChannelRemover\ClientCapIdleKickCommand;
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

/** @var TeamSpeak3_Node_server $teamspeakServer */
$teamspeakServer = $container->get('teamspeak_server');

$application->addCommands([
    new ClientCapIdleKickCommand(
        $teamspeakServer,
        $container->getParameter('afk_kick.base_channel'),
        $container->getParameter('afk_kick.excludes'),
        $container->getParameter('afk_kick.idle_mins'),
        $container->getParameter('afk_kick.user_count')
    ),
    new RemoveIdleChannelsCommand(
        $teamspeakServer,
        $container->getParameter('remove_idle.base_channel'),
        $container->getParameter('remove_idle.excludes'),
        $container->getParameter('remove_idle.idle_mins')
   ),
    new ShuffleChannelsCommand(
        $teamspeakServer,
        $container->getParameter('shuffle.base_channel'),
        $container->getParameter('shuffle.excludes')
    ),
    new ListChannelsCommand(
        $teamspeakServer
    )
]);

$application->run();