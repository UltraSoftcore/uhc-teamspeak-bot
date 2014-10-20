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

### Idle player kick

`clients:kickIdle`

Kicks all 'idle' clients when the server is past a threshold of clients

Configuration:

    afk_kick.base_channel: 202   # The channel (and subchannels) in which clients can be kicked from
    afk_kick.user_count: 360     # The client count before kicking takes place
    afk_kick.excludes: []        # Array of immune channels under the base channel
    afk_kick.idle_mins: 30       # Number of minutes to be considered idle

Configuration
-------------

    teamspeak.username: serveradmin     # Account to use to login
    teamspeak.password: password        # Password to use to login
    teamspeak.host: uhc.gg              # Server address to use
    teamspeak.server_port: 9987         # Port of virtual server to connect to
    teamspeak.query_port: 10011         # Query port of the server