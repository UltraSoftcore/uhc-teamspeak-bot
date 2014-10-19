<?php
namespace Eluinhost\TSChannelRemover;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use TeamSpeak3_Node_Channel;
use TeamSpeak3_Node_Client;
use TeamSpeak3_Node_Server;

class ChannelRemoveCommand extends Command {

    /** @var TeamSpeak3_Node_Server */
    private $server;

    /** @var int */
    private $channelID;

    /** @var int[] */
    private $excludes;

    /** @var int */
    private $allowedMins;

    public function __construct(Container $container)
    {
        $this->server = $container->get('teamspeak_server');
        $this->channelID = $container->getParameter('teamspeak.channelID');
        $this->excludes = $container->getParameter('teamspeak.excludes');
        $this->allowedMins = $container->getParameter('teamspeak.allowedMins');
        parent::__construct('channels:removeIdle');
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->addOption('shuffle', null, InputOption::VALUE_NONE);
    }

    protected function getChannelList()
    {
        $channel = $this->server->channelGetById($this->channelID);
        return $channel->subChannelList();
    }

    protected function removeIdlers(OutputInterface $output)
    {
        $deleted = 0;
        $list = $this->getChannelList();
        /** @var $channel Teamspeak3_Node_Channel */
        foreach($list as $channel) {
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
                $channel->message('Channel deleted due to every client being idle too long');
                $channel->delete(true);
                $output->writeln('Channel delteted: ' . $channel->getProperty('channel_name'));
            }
        }
        $output->writeln($deleted . ' channels deleted');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->removeIdlers($output);
        if($input->getOption('shuffle')) {
            $this->shuffleChannels();
        }
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