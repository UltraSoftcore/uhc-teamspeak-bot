<?php
namespace Eluinhost\TSChannelRemover;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamSpeak3;
use TeamSpeak3_Node_Channel;
use TeamSpeak3_Node_Client;
use TeamSpeak3_Node_Server;

class ClientCapIdleKickCommand extends Command {

    public function __construct(TeamSpeak3_Node_Server $server, TeamspeakHelper $helper, array $baseIDs, array $excludes, $idleMins, $userCount, array $protectedGroups)
    {
        $this->server = $server;
        $this->baseIDs = $baseIDs;
        $this->helper = $helper;
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
        //get all the chosen base channels
        $base_channels = count($this->baseIDs) == 0 ? null : $this->helper->getChannels($this->baseIDs);

        //add all of their children recursive
        $all_channels = $this->helper->getEntireTree($base_channels);

        //remove the excluded channels
        foreach($this->excludes as $exclude) {
            unset($all_channels[$exclude]);
        }

        return $all_channels;
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
                //if they're a protected user, skip them
                if(array_search($client->getInfo()['client_unique_identifier'], $protected)) {
                    continue;
                }

                //if they're idle add them to the list of idle users
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
            $client = $this->server->clientGetById($idler);
            $output->writeln('Kicked client: ' . $client . ' (' . $idler . ') from ' . $this->server->channelGetById($client['cid'])->getPathway());
            $this->server->clientKick($idler, TeamSpeak3::KICK_SERVER, 'Kicked due to idling and server requiring space');
        }

        $output->writeln('Kicked ' . count($idleList) . ' total clients');
    }
} 