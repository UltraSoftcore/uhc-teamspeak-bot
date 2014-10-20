TS-idle-kicker
==============

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

    remove_idle.base_channel: 1350  # Base channel whose subchannels are checked
    remove_idle.excludes: []        # Array of immune channels under the base channel
    remove_idle.idle_mins: 30       # Number of minute to be considered idle

### Shuffle Channels

`channels:shuffle`

Shuffles all channels

Configuration:

    shuffle.base_channel: 1350      # Base channel whose subchannels are shuffled
    shuffle.excludes: []            # Array of immune channels under the base channel

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