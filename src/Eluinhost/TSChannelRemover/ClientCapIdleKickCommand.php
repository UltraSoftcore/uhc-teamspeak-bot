<?php
namespace Eluinhost\TSChannelRemover;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamSpeak3_Node_Channel;
use TeamSpeak3_Node_Client;
use TeamSpeak3_Node_Server;

class ClientCapIdleKickCommand extends Command {

    public function __construct(TeamSpeak3_Node_Server $server, $baseID, array $excludes, $idleMins, $userCount, array $protectedGroups)
    {
        $this->server = $server;
        $this->channelID = $baseID;
        $this->excludes = $excludes;
        $this->userCount = $userCount;
        $this->allowedMins = $idleMins;
        $this->protectedGroups = $protectedGroups;

        parent::__construct('clients:kickIdle');
    }

    protected function getProtectedUuids()
    {
        $uuids = [];
        foreach($this->protectedGroups as $group) {
            $clients = $this->server->serverGroupClientList($group);

            foreach($clients as $p) {
                array_push($uuids, $p['client_unique_identifier']);
            }
        }
        return $uuids;
    }

    /**
     * Get all of the channels we want to use
     *
     * @return Teamspeak3_Node_Channel[]
     */
    protected function getChannelList()
    {
        $base = $this->server->channelGetById($this->channelID);

        $channels = $this->getEntireTree($base);

        foreach($this->excludes as $exclude) {
            unset($channels[$exclude]);
        }

        return $channels;
    }

    /**
     * Returns all the channels below and including the given channel. ID => TeamSpeak3_Node_Channel
     *
     * @param TeamSpeak3_Node_Channel $channel
     * @return TeamSpeak3_Node_Channel[]
     */
    protected function getEntireTree(TeamSpeak3_Node_Channel $channel)
    {
        $allChannels = [];
        /** @var TeamSpeak3_Node_Channel $child */
        foreach ($channel->subChannelList() as $child) {
            $allChannels[$child->getId()] = $child;
            array_replace($this->getEntireTree($child), $allChannels);
        }
        $allChannels[$channel->getId()] = $channel;
        return $allChannels;
    }

    /**
     * @param TeamSpeak3_Node_Channel[] $channels
     * @return int[]
     */
    protected function getIdleUserIds(array $channels)
    {
        $protected = $this->getProtectedUuids();
        $idlers = [];
        foreach($channels as $channel) {
            /** @var TeamSpeak3_Node_Client $client */
            foreach($channel->clientList() as $client) {
                if(array_search($client->getInfo()['client_unique_identifier'], $protected)) {
                    continue;
                }

                if($client['client_idle_time']/1000/60 >= $this->allowedMins) {
                    array_push($idlers, $client->getId());
                }
            }
        }
        return $idlers;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $total = $this->server->clientCount();
        $amountToKick = $total - $this->userCount;

        $output->writeln('Currently at ' . $total . '/' . $this->userCount . ' (' . ($amountToKick < 0 ? '-' : '+') . $amountToKick . ')');

        if($amountToKick < 1) {
            $output->writeln('Not at player cap, skipping idle kick');
            return;
        }

        $idleList = $this->getIdleUserIds($this->getChannelList());

        //if there are less people to kick than idlers, choose random clients to make up the cap
        if($amountToKick < $idleList) {
            shuffle($idleList);
            array_splice($idleList, $amountToKick);
        }

        foreach($idleList as $idler) {
            $output->writeln('Kicked client: ' . $this->server->clientGetById($idler) . ' (' . $idler . ')');
            $this->server->clientKick($idler, TeamSpeak3::KICK_SERVER, 'Kicked due to idling and server requiring space');
        }

        $output->writeln('Kicked ' . count($idleList) . ' total clients');
    }
} 