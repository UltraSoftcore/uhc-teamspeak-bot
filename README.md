TS-idle-kicker
==============

Install:

- `composer install`
- Copy config.yml.dist to config.yml

Usage:

`php run.php <command>`

Commands
--------

### Remove Idle users

`channels:removeIdle`

Removes all channels with all the user inside of it 'idle'

Configuration:

teamspeak.channelID - ID of the channel which all subchannels of which will be considered for removal
teamspeak.excludes - IDs of channels to ignore
teamspeak.allowedMins - Number of minutes before being considered idle

### Shuffle Channels

`channels:shuffle`

Shuffles all channels

Configuration:

teamspeak.channelID - ID of the channel which all subchannels of which will be shuffled
teamspeak.excludes - IDs of channels not to shuffle, they should all be at the top of the list

Configuration
-------------

    teamspeak.username: serveradmin     # Account to use to login
    teamspeak.password: password        # Password to use to login
    teamspeak.host: uhc.gg              # Server address to use
    teamspeak.server_port: 9987         # Port of virtual server to connect to
    teamspeak.query_port: 10011         # Query port of the server