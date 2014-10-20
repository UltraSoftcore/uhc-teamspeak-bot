<?php
namespace Eluinhost\TSChannelRemover;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamSpeak3_Node_Channel;
use TeamSpeak3_Node_Client;
use TeamSpeak3_Node_Server;

class RemoveIdleChannelsCommand extends Command {

    public function __construct(TeamSpeak3_Node_Server $server, TeamspeakHelper $helper, array $baseIDs, array $excludes, $idleMins)
    {
        $this->server = $server;
        $this->helper = $helper;
        $this->baseIDs = $baseIDs;
        $this->excludes = $excludes;
        $this->allowedMins = $idleMins;

        parent::__construct('channels:removeIdle');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $deleted = 0;

        //get all the channels we want to check
        $base_channels = count($this->baseIDs) == 0 ? null : $this->helper->getChannels($this->baseIDs);

        //get all their subchannels too
        $list = $this->helper->getEntireTree($base_channels);

        /** @var $channel Teamspeak3_Node_Channel */
        foreach($list as $channel) {
            //if it's a protected channel don't do anything
            if(array_search($channel->getId(), $this->excludes) !== false) {
                continue;
            }

            $clientList = $channel->clientList();

            $channelDeletable = true;
            /** @var $client Teamspeak3_Node_Client */
            foreach($clientList as $client) {
                $idleMins = $client["client_idle_time"]/1000/60;
                if($idleMins < $this->allowedMins) {
                    $channelDeletable = false;
                    break;
                }
            }

            if($channelDeletable) {
                $deleted++;
                //$channel->message('Channel deleted due to every client being idle too long');
                //$channel->delete(true);
                $output->writeln('Channel delteted: ' . $channel->getPathway());
            }
        }
        $output->writeln($deleted . ' channels deleted');
    }
} 