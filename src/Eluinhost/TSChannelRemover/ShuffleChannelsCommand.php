<?php
namespace Eluinhost\TSChannelRemover;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamSpeak3_Node_Channel;
use TeamSpeak3_Node_Server;

class ShuffleChannelsCommand extends Command {

    public function __construct(TeamSpeak3_Node_Server $server, TeamspeakHelper $helper, array $baseIDs, array $excludes)
    {
        $this->server = $server;
        $this->helper = $helper;
        $this->baseIDs= $baseIDs;
        $this->excludes = $excludes;

        parent::__construct('channels:shuffle');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting shuffle, protected channels: ' . json_encode($this->excludes));

        $base_channels = $this->helper->getChannels($this->baseIDs);

        foreach($base_channels as $channel) {
            $this->shuffleChannel($channel, $output);
        }

        $output->writeln('Shuffled all channels');
    }

    protected function shuffleChannel(TeamSpeak3_Node_Channel $channel, OutputInterface $output)
    {
        $output->writeln('Shuffling subchannels of: ' . $channel->getPathway());
        $fullList = $channel->subChannelList();

        $excluded = [];

        $output->writeln('Current channel order:');
        //filter out the non protected channels
        $toShuffle = array_filter($fullList, function(TeamSpeak3_Node_Channel $channel) use (&$excluded, $output) {
            $output->writeln($channel->getPathway() . ' (' . $channel->getId() . ')');

            //if it's not protected add to the shuffle list
            if(array_search($channel->getId(), $this->excludes) === false) {
                return true;
            }

            //if it is protected add directly to final list
            array_push($excluded, $channel);
            return false;
        });

        shuffle($toShuffle);

        //add the shuffled list on after the excluded channels
        $final = array_merge($excluded, $toShuffle);

        $output->writeln('New channel order:');
        array_reduce($final, function($carry, TeamSpeak3_Node_Channel $channel) use ($output) {
            $output->writeln($channel->getPathway() . ' (' . $channel->getId() . ')');

            $channel->modify(['channel_order' => $carry]);

            //return current channel ID to pass onto next iteration
            return $channel->getId();
        }, 0);

        $output->writeln('Channel shuffle complete');
    }
} 