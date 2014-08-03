#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use Eluinhot\TSChannelRemover\ChannelRemoverApplication;

$application = new ChannelRemoverApplication();
$application->run();