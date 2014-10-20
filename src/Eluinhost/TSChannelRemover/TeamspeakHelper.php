<?php
/**
 * Created by IntelliJ IDEA.
 * User: HowdenG
 * Date: 20/10/2014
 * Time: 14:40
 */

namespace Eluinhost\TSChannelRemover;


use TeamSpeak3_Node_Channel;
use TeamSpeak3_Node_Server;

class TeamspeakHelper {

    /**
     * @var TeamSpeak3_Node_Server
     */
    private $server;

    public function __construct(TeamSpeak3_Node_Server $server)
    {
        $this->server = $server;
    }

    /**
     * Get all the root level channels
     *
     * @return TeamSpeak3_Node_Channel[]
     */
    public function getRootChannels()
    {
        return array_filter($this->server->channelList(), function(TeamSpeak3_Node_Channel $channel) {
            return $channel->getLevel() === 0;
        });
    }

    /**
     * Get all channels below the given channel/s recursively plus supplied channel/s
     *
     * If $channel is null will use all of the server's root channels
     *
     * @param null|TeamSpeak3_Node_Channel|TeamSpeak3_Node_Channel[] $channels
     * @return Teamspeak3_Node_Channel[] Map of channel_id => TeamSpeak3_Node_Channel
     */
    public function getEntireTree($channels = null)
    {
        if($channels === null) {
            $channels = $this->getRootChannels();
        } else if(!is_array($channels)) {
            // make it an array if it isn't already
            $channels = [$channels];
        }

        $allChannels = [];

        foreach($channels as $channel) {
            /** @var TeamSpeak3_Node_Channel $child */
            foreach ($channel->subChannelList() as $child) {
                $allChannels[$child->getId()] = $child;
                array_replace($this->getEntireTree($child), $allChannels);
            }
            $allChannels[$channel->getId()] = $channel;
        }
        return $allChannels;
    }

    /**
     * Convert array of channel IDs to actual channels
     *
     * @param array $ids
     * @return TeamSpeak3_Node_Channel[]
     */
    public function getChannels(array $ids)
    {
        return array_map(function($element) {
            return $this->server->channelGetById($element);
        }, $ids);
    }
} 