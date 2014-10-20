uhc-teamspeak-bot
=================

Install:

- `composer install`
- Copy config.yml.dist to config.yml and fill out required details

Usage:

`php run.php <command>`

Commands
--------

### List Channels

`channels:list`

Lists all the channels on the server and their IDs

### Remove Idle users

`channels:removeIdle`

Removes all channels with all the user inside of it 'idle'

Configuration:

    remove_idle.base_channels: [1350]  # Array of base channel whose subchannels are checked (including the channel itself). If empty uses ALL the root channels of the server
    remove_idle.excludes: [1350]       # Array of immune channels under the base channel, subchannels will still be processed
    remove_idle.idle_mins: 30          # Number of minute to be considered idle

### Shuffle Channels

`channels:shuffle`

Shuffles all channels

Configuration:

    shuffle.base_channels: [1350]   # Array of base channels whose subchannels are shuffled (non-recursive).
    shuffle.excludes: [6039, 196]   # Array of immune channels that will keep their order and be placed at the top of the shuffled list

### Idle player kick

`clients:kickIdle`

Kicks all 'idle' clients when the server is past a threshold of clients

Configuration:

    afk_kick.base_channels: [202] # Array of channels (and their subchannels recursively) in which clients can be kicked from. Empty array counts as all the root channels (therefore entire server)
    afk_kick.user_count: 360      # The client count before kicking takes place
    afk_kick.excludes: []         # Array of immune channels under the base channel
    afk_kick.idle_mins: 30        # Number of minutes to be considered idle

Configuration
-------------

    teamspeak.username: serveradmin     # Account to use to login
    teamspeak.password: password        # Password to use to login
    teamspeak.host: uhc.gg              # Server address to use
    teamspeak.server_port: 9987         # Port of virtual server to connect to
    teamspeak.query_port: 10011         # Query port of the server