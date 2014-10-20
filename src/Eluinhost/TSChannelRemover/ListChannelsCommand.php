<?php
namespace Eluinhost\TSChannelRemover;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamSpeak3_Node_Channel;
use TeamSpeak3_Node_Server;

class ListChannelsCommand extends Command {

    public function __construct(TeamSpeak3_Node_Server $server)
    {
        $this->server = $server;

        parent::__construct('channels:list');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Teamspeak3_Node_Channel[] $channels */
        $channels = $this->server->channelList();

        foreach($channels as $channel) {
            $output->writeln("{$channel->getId()} - {$channel->getPathway()}");
        }
    }
} 