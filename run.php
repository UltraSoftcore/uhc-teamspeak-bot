#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use Eluinhot\TSChannelRemover\ChannelRemoverApplication;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
$loader->load('config.yml');

$container->get('application')->run();