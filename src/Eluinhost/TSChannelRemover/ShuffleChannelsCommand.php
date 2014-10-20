<?php
namespace Eluinhost\TSChannelRemover;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamSpeak3_Node_Channel;
use TeamSpeak3_Node_Server;

class ShuffleChannelsCommand extends Command {

    public function __construct(TeamSpeak3_Node_Server $server, $baseID, array $excludes)
    {
        $this->server = $server;
        $this->channelID = $baseID;
        $this->excludes = $excludes;

        parent::__construct('channels:shuffle');
    }

    protected function getChannelList()
    {
        $channel = $this->server->channelGetById($this->channelID);
        return $channel->subChannelList();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->shuffleChannels();
    }

    private function getLastChannelSort()
    {
        $excludeCount = count($this->excludes);

        return $excludeCount === 0 ? 0 : $this->excludes[$excludeCount -1];
    }

    private function shuffleChannels()
    {
        $lastSort = $this->getLastChannelSort();

        $list = $this->getChannelList();
        $list = array_filter($list, function(TeamSpeak3_Node_Channel $channel) {
            return array_search($channel->getId(), $this->excludes) === false;
        });

        shuffle($list);
        /** @var $channel Teamspeak3_Node_Channel */
        foreach($list as $channel) {
            $channel->modify([
                'channel_order' => $lastSort
            ]);
            $lastSort = $channel->getId();
        }
    }
} 