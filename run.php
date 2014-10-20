#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use Eluinhost\TSChannelRemover\ClientCapIdleKickCommand;
use Eluinhost\TSChannelRemover\ListChannelsCommand;
use Eluinhost\TSChannelRemover\RemoveIdleChannelsCommand;
use Eluinhost\TSChannelRemover\ShuffleChannelsCommand;
use Eluinhost\TSChannelRemover\TeamspeakHelper;
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
/** @var TeamspeakHelper $teamspeakHelper */
$teamspeakHelper = $container->get('teamspeak_helper');

$application->addCommands([
    new ClientCapIdleKickCommand(
        $teamspeakServer,
        $teamspeakHelper,
        $container->getParameter('afk_kick.base_channels'),
        $container->getParameter('afk_kick.excludes'),
        $container->getParameter('afk_kick.idle_mins'),
        $container->getParameter('afk_kick.user_count'),
        $container->getParameter('afk_kick.protected_groups')
    ),
    new RemoveIdleChannelsCommand(
        $teamspeakServer,
        $teamspeakHelper,
        $container->getParameter('remove_idle.base_channels'),
        $container->getParameter('remove_idle.excludes'),
        $container->getParameter('remove_idle.idle_mins')
   ),
    new ShuffleChannelsCommand(
        $teamspeakServer,
        $teamspeakHelper,
        $container->getParameter('shuffle.base_channels'),
        $container->getParameter('shuffle.excludes')
    ),
    new ListChannelsCommand(
        $teamspeakServer
    )
]);

$application->run();